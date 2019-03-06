<?php

namespace Code16\Sharp\Tests\Unit\EntityList;

use Code16\Sharp\EntityList\Containers\EntityListDataContainer;
use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Tests\Fixtures\Person;
use Code16\Sharp\Tests\Unit\Form\Eloquent\SharpFormEloquentBaseTest;
use Code16\Sharp\Utils\Transformers\SharpAttributeTransformer;
use Code16\Sharp\Utils\Transformers\WithCustomTransformers;
use Illuminate\Support\Facades\DB;

class WithCustomTransformersInEntityListTest extends SharpFormEloquentBaseTest
{
    /** @test */
    function we_can_retrieve_an_array_version_of_a_models_collection()
    {
        Person::create(["name" => "John Wayne"]);
        Person::create(["name" => "Mary Wayne"]);

        $list = new WithCustomTransformersTestList();

        $this->assertArrayContainsSubset(
            [["name" => "John Wayne"], ["name" => "Mary Wayne"]],
            $list->getListData(new EntityListQueryParams())
        );
    }
    
    /** @test */
    function we_can_retrieve_an_array_version_of_a_db_raw_collection()
    {
        Person::create(["name" => "John Wayne"]);
        Person::create(["name" => "Mary Wayne"]);
        
        $list = new class extends WithCustomTransformersTestList
        {
            function getListData(EntityListQueryParams $params)
            {
                return $this->transform(DB::table((new Person())->getTable())->get());
            }
        };
    
        $this->assertArrayContainsSubset(
            [["name" => "John Wayne"], ["name" => "Mary Wayne"]],
            $list->getListData(new EntityListQueryParams())
        );
    }
    
    /** @test */
    function we_can_retrieve_an_array_version_of_a_db_raw_paginator()
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
                return $this->transform(DB::table((new Person())->getTable())->paginate(2));
            }
        };
        
        $this->assertArrayContainsSubset(
            [["name" => "A"], ["name" => "B"]],
            $list->getListData(new EntityListQueryParams())->items()
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

        $this->assertArrayContainsSubset(
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

        $this->assertArrayContainsSubset(
            ["name" => "Mary Wayne", "mother:name" => "Jane Wayne"],
            $list->getListData(new EntityListQueryParams())[1]
        );

        $this->assertArrayContainsSubset(
            ["name" => "John Wayne", "mother:name" => null],
            $list->getListData(new EntityListQueryParams())[2]
        );
    }

    /** @test */
    function we_can_define_a_custom_transformer_as_a_closure()
    {
        Person::create(["name" => "John Wayne"]);

        $list = new class extends WithCustomTransformersTestList
        {
            function getListData(EntityListQueryParams $params)
            {
                return $this->setCustomTransformer("name", function($name) {
                    return strtoupper($name);
                })->transform(Person::all());
            }
        };

        $this->assertArrayContainsSubset(
            ["name" => "JOHN WAYNE"],
            $list->getListData(new EntityListQueryParams())[0]
        );
    }

    /** @test */
    function we_can_define_a_custom_transformer_as_a_class_name()
    {
        Person::create(["name" => "John Wayne"]);

        $list = new class extends WithCustomTransformersTestList
        {
            function getListData(EntityListQueryParams $params)
            {
                return $this->setCustomTransformer("name", UppercaseTransformer::class)
                    ->transform(Person::all());
            }
        };

        $this->assertArrayContainsSubset(
            ["name" => "JOHN WAYNE"],
            $list->getListData(new EntityListQueryParams())[0]
        );
    }

    /** @test */
    function we_can_define_a_custom_transformer_as_a_class_instance()
    {
        Person::create(["name" => "John Wayne"]);

        $list = new class extends WithCustomTransformersTestList
        {
            function getListData(EntityListQueryParams $params)
            {
                return $this->setCustomTransformer("name", new UppercaseTransformer())
                    ->transform(Person::all());
            }
        };

        $this->assertArrayContainsSubset(
            ["name" => "JOHN WAYNE"],
            $list->getListData(new EntityListQueryParams())[0]
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

class UppercaseTransformer implements SharpAttributeTransformer
{

    /**
     * Transform a model attribute to array (json-able).
     *
     * @param $value
     * @param $instance
     * @param string $attribute
     * @return mixed
     */
    function apply($value, $instance = null, $attribute = null)
    {
        return strtoupper($value);
    }
}