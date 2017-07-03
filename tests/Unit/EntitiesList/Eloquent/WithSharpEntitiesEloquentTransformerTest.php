<?php

namespace Code16\Sharp\Tests\Unit\EntitiesList\Eloquent;

use Code16\Sharp\EntitiesList\Containers\EntitiesListDataContainer;
use Code16\Sharp\EntitiesList\Eloquent\WithSharpEntitiesListEloquentTransformer;
use Code16\Sharp\EntitiesList\EntitiesListQueryParams;
use Code16\Sharp\EntitiesList\SharpEntitiesList;
use Code16\Sharp\Tests\Fixtures\Person;
use Code16\Sharp\Tests\Unit\Form\Eloquent\SharpFormEloquentBaseTest;

class WithSharpEntitiesEloquentTransformerTest extends SharpFormEloquentBaseTest
{
    /** @test */
    function we_can_retrieve_an_array_version_of_a_models_collection()
    {
        Person::create(["name" => "John Wayne"]);
        Person::create(["name" => "Mary Wayne"]);

        $list = new WithSharpEntitiesEloquentTransformerTestList();

        $this->assertArraySubset(
            [["name" => "John Wayne"], ["name" => "Mary Wayne"]],
            $list->getListData(new EntitiesListQueryParams())
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

        $list = new class extends WithSharpEntitiesEloquentTransformerTestList {
            function getListData(EntitiesListQueryParams $params) {
                return $this->transform(Person::paginate(2));
            }
        };

        $this->assertArraySubset(
            [["name" => "A"], ["name" => "B"]],
            $list->getListData(new EntitiesListQueryParams())->items()
        );
    }

    /** @test */
    function we_handle_the_relation_separator()
    {
        $mother = Person::create(["name" => "Jane Wayne"]);
        Person::create(["name" => "Mary Wayne", "mother_id" => $mother->id]);
        Person::create(["name" => "John Wayne"]);

        $list = new class extends WithSharpEntitiesEloquentTransformerTestList {
            function buildListDataContainers() {
                $this->addDataContainer(EntitiesListDataContainer::make("mother:name"));
            }
        };

        $this->assertArraySubset(
            ["name" => "Mary Wayne", "mother:name" => "Jane Wayne"],
            $list->getListData(new EntitiesListQueryParams())[1]
        );

        $this->assertArraySubset(
            ["name" => "John Wayne", "mother:name" => null],
            $list->getListData(new EntitiesListQueryParams())[2]
        );
    }
}

class WithSharpEntitiesEloquentTransformerTestList extends SharpEntitiesList
{
    use WithSharpEntitiesListEloquentTransformer;

    function getListData(EntitiesListQueryParams $params)
    {
        return $this->transform(Person::all());
    }

    function buildListDataContainers() {}
    function buildListLayout() {}
    function buildListConfig() {}
}