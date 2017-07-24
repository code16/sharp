<?php

namespace Code16\Sharp\Tests\Unit\EntityList;

use Code16\Sharp\EntityList\Containers\EntityListDataContainer;
use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Tests\Fixtures\Person;
use Code16\Sharp\Tests\Unit\Form\Eloquent\SharpFormEloquentBaseTest;
use Code16\Sharp\Utils\Transformers\WithCustomTransformers;

class WithCustomTransformersInEntityListTest extends SharpFormEloquentBaseTest
{
    /** @test */
    function we_can_retrieve_an_array_version_of_a_models_collection()
    {
        Person::create(["name" => "John Wayne"]);
        Person::create(["name" => "Mary Wayne"]);

        $list = new WithCustomTransformersTestList();

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

        $list = new class extends WithCustomTransformersTestList
        {
            function getListData(EntityListQueryParams $params)
            {
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

        $list = new class extends WithCustomTransformersTestList
        {
            function buildListDataContainers()
            {
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

class WithCustomTransformersTestList extends SharpEntityList
{
    use WithCustomTransformers;

    function getListData(EntityListQueryParams $params)
    {
        return $this->transform(Person::all());
    }

    function buildListDataContainers() {}
    function buildListLayout() {}
    function buildListConfig() {}
}