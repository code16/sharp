<?php

namespace Code16\Sharp\Tests\Unit\EntitiesList\Eloquent;

use Code16\Sharp\EntityList\Containers\EntityListDataContainer;
use Code16\Sharp\EntityList\Eloquent\WithSharpEntityListEloquentTransformer;
use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Tests\Fixtures\Person;
use Code16\Sharp\Tests\Unit\Form\Eloquent\SharpFormEloquentBaseTest;

class WithSharpEntitiesEloquentTransformerTest extends SharpFormEloquentBaseTest
{
    /** @test */
    function we_can_retrieve_an_array_version_of_a_models_collection()
    {
        Person::create(["name" => "John Wayne"]);
        Person::create(["name" => "Mary Wayne"]);

        $list = new WithSharpEntityEloquentTransformerTestList();

        $this->assertArraySubset(
            [["name" => "John Wayne"], ["name" => "Mary Wayne"]],
            $list->getListData(new EntityListQueryParams())
        );
    }

    /** @test */
    function we_can_retrieve_an_array_version_of_a_models_paginator()
    {
        Person::create(["name" => "A"]);
        Person::create(["name" => "B"]);
        Person::create(["name" => "C"]);
        Person::create(["name" => "D"]);
        Person::create(["name" => "E"]);

        $list = new class extends WithSharpEntityEloquentTransformerTestList {
            function getListData(EntityListQueryParams $params) {
                return $this->transform(Person::paginate(2));
            }
        };

        $this->assertArraySubset(
            [["name" => "A"], ["name" => "B"]],
            $list->getListData(new EntityListQueryParams())->items()
        );
    }

    /** @test */
    function we_handle_the_relation_separator()
    {
        $mother = Person::create(["name" => "Jane Wayne"]);
        Person::create(["name" => "Mary Wayne", "mother_id" => $mother->id]);
        Person::create(["name" => "John Wayne"]);

        $list = new class extends WithSharpEntityEloquentTransformerTestList {
            function buildListDataContainers() {
                $this->addDataContainer(EntityListDataContainer::make("mother:name"));
            }
        };

        $this->assertArraySubset(
            ["name" => "Mary Wayne", "mother:name" => "Jane Wayne"],
            $list->getListData(new EntityListQueryParams())[1]
        );

        $this->assertArraySubset(
            ["name" => "John Wayne", "mother:name" => null],
            $list->getListData(new EntityListQueryParams())[2]
        );
    }
}

class WithSharpEntityEloquentTransformerTestList extends SharpEntityList
{
    use WithSharpEntityListEloquentTransformer;

    function getListData(EntityListQueryParams $params)
    {
        return $this->transform(Person::all());
    }

    function buildListDataContainers() {}
    function buildListLayout() {}
    function buildListConfig() {}
}