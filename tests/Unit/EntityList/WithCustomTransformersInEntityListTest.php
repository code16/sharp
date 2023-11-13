<?php

namespace Code16\Sharp\Tests\Unit\EntityList;

use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\EntityList\Fields\EntityListFieldsLayout;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Tests\Fixtures\Person;
use Code16\Sharp\Tests\Unit\SharpEloquentBaseTest;
use Code16\Sharp\Utils\Transformers\SharpAttributeTransformer;
use Code16\Sharp\Utils\Transformers\WithCustomTransformers;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\DB;

class WithCustomTransformersInEntityListTest extends SharpEloquentBaseTest
{
    /** @test */
    public function we_can_retrieve_an_array_version_of_a_models_collection()
    {
        Person::create(['name' => 'John Wayne']);
        Person::create(['name' => 'Mary Wayne']);

        $list = new WithCustomTransformersTestList();

        $this->assertArraySubset(
            [['name' => 'John Wayne'], ['name' => 'Mary Wayne']],
            $list->getListData(),
        );
    }

    /** @test */
    public function we_can_retrieve_an_array_version_of_a_db_raw_collection()
    {
        Person::create(['name' => 'John Wayne']);
        Person::create(['name' => 'Mary Wayne']);

        $list = new class extends WithCustomTransformersTestList
        {
            public function getListData(): array|Arrayable
            {
                return $this->transform(DB::table((new Person())->getTable())->get());
            }
        };

        $this->assertArraySubset(
            [['name' => 'John Wayne'], ['name' => 'Mary Wayne']],
            $list->getListData(),
        );
    }

    /** @test */
    public function we_can_retrieve_an_array_version_of_a_db_raw_paginator()
    {
        Person::create(['name' => 'A']);
        Person::create(['name' => 'B']);
        Person::create(['name' => 'C']);
        Person::create(['name' => 'D']);
        Person::create(['name' => 'E']);

        $list = new class extends WithCustomTransformersTestList
        {
            public function getListData(): array|Arrayable
            {
                return $this->transform(DB::table((new Person())->getTable())->paginate(2));
            }
        };

        $this->assertArraySubset(
            [['name' => 'A'], ['name' => 'B']],
            $list->getListData()->items(),
        );
    }

    /** @test */
    public function we_can_retrieve_an_array_version_of_a_models_paginator()
    {
        Person::create(['name' => 'A']);
        Person::create(['name' => 'B']);
        Person::create(['name' => 'C']);
        Person::create(['name' => 'D']);
        Person::create(['name' => 'E']);

        $list = new class extends WithCustomTransformersTestList
        {
            public function getListData(): array|Arrayable
            {
                return $this->transform(Person::paginate(2));
            }
        };

        $this->assertArraySubset(
            [['name' => 'A'], ['name' => 'B']],
            $list->getListData()->items(),
        );
    }

    /** @test */
    public function we_handle_the_relation_separator()
    {
        $mother = Person::create(['name' => 'Jane Wayne']);
        Person::create(['name' => 'Mary Wayne', 'mother_id' => $mother->id]);
        Person::create(['name' => 'John Wayne']);

        $list = new class extends WithCustomTransformersTestList
        {
            public function buildListFields(EntityListFieldsContainer $fieldsContainer): void
            {
                $fieldsContainer
                    ->addField(EntityListField::make('mother:name'));
            }
        };

        $this->assertArraySubset(
            ['name' => 'Mary Wayne', 'mother:name' => 'Jane Wayne'],
            $list->getListData()[1],
        );

        $this->assertArraySubset(
            ['name' => 'John Wayne', 'mother:name' => null],
            $list->getListData()[2],
        );
    }

    /** @test */
    public function we_can_define_a_custom_transformer_as_a_closure()
    {
        Person::create(['name' => 'John Wayne']);

        $list = new class extends WithCustomTransformersTestList
        {
            public function getListData(): array|Arrayable
            {
                return $this
                    ->setCustomTransformer('name', function ($name) {
                        return strtoupper($name);
                    })
                    ->transform(Person::all());
            }
        };

        $this->assertArraySubset(
            ['name' => 'JOHN WAYNE'],
            $list->getListData()[0],
        );
    }

    /** @test */
    public function we_can_define_a_custom_transformer_as_a_class_name()
    {
        Person::create(['name' => 'John Wayne']);

        $list = new class extends WithCustomTransformersTestList
        {
            public function getListData(): array|Arrayable
            {
                return $this
                    ->setCustomTransformer('name', UppercaseTransformer::class)
                    ->transform(Person::all());
            }
        };

        $this->assertArraySubset(
            ['name' => 'JOHN WAYNE'],
            $list->getListData()[0],
        );
    }

    /** @test */
    public function we_can_define_a_custom_transformer_as_a_class_instance()
    {
        Person::create(['name' => 'John Wayne']);

        $list = new class extends WithCustomTransformersTestList
        {
            public function getListData(): array|Arrayable
            {
                return $this
                    ->setCustomTransformer('name', new UppercaseTransformer())
                    ->transform(Person::all());
            }
        };

        $this->assertArraySubset(
            ['name' => 'JOHN WAYNE'],
            $list->getListData()[0],
        );
    }
}

class WithCustomTransformersTestList extends SharpEntityList
{
    use WithCustomTransformers;

    public function getListData(): array|Arrayable
    {
        return $this->transform(Person::all());
    }

    public function buildListFields(EntityListFieldsContainer $fieldsContainer): void
    {
    }

    public function buildListLayout(EntityListFieldsLayout $fieldsLayout): void
    {
    }

    public function buildListConfig(): void
    {
    }
}

class UppercaseTransformer implements SharpAttributeTransformer
{
    /**
     * Transform a model attribute to array (json-able).
     *
     * @param  $value
     * @param  $instance
     * @param  string  $attribute
     * @return mixed
     */
    public function apply($value, $instance = null, $attribute = null)
    {
        return strtoupper($value);
    }
}
