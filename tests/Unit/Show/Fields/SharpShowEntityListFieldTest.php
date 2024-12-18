<?php

namespace Code16\Sharp\Tests\Unit\Show\Fields;

use Code16\Sharp\Show\Fields\SharpShowEntityListField;
use Code16\Sharp\Tests\SharpTestCase;

class SharpShowEntityListFieldTest extends SharpTestCase
{
    /** @test */
    public function we_can_define_entity_list_field()
    {
        $field = SharpShowEntityListField::make('entityListField', 'entityKey');

        $this->assertEquals(
            [
                'key' => 'entityListField',
                'type' => 'entityList',
                'entityListKey' => 'entityKey',
                'showEntityState' => true,
                'showCreateButton' => true,
                'showReorderButton' => true,
                'showSearchField' => true,
                'emptyVisible' => false,
                'showCount' => false,
                'hiddenCommands' => ['entity' => [], 'instance' => []],
            ],
            $field->toArray(),
        );
    }

    /** @test */
    public function we_can_define_entity_list_field_with_default_key()
    {
        $field = SharpShowEntityListField::make('instances');

        $this->assertEquals(
            [
                'key' => 'instances',
                'type' => 'entityList',
                'entityListKey' => 'instances',
                'showEntityState' => true,
                'showCreateButton' => true,
                'showReorderButton' => true,
                'showSearchField' => true,
                'emptyVisible' => false,
                'showCount' => false,
                'hiddenCommands' => ['entity' => [], 'instance' => []],
            ],
            $field->toArray(),
        );
    }

    /** @test */
    public function we_can_define_hide_filter_with_value()
    {
        $field = SharpShowEntityListField::make('entityListField', 'entityKey')
            ->hideFilterWithValue('f1', 'value1');

        $this->assertArraySubset(
            [
                'hiddenFilters' => [
                    'f1' => 'value1',
                ],
            ],
            $field->toArray(),
        );
    }

    /** @test */
    public function we_can_define_hide_filter_with_value_with_a_callable()
    {
        $field = SharpShowEntityListField::make('entityListField', 'entityKey')
            ->hideFilterWithValue('f1', function ($instanceId) {
                return 'computed';
            });

        $this->assertArraySubset(
            [
                'hiddenFilters' => [
                    'f1' => 'computed',
                ],
            ],
            $field->toArray(),
        );
    }

    /** @test */
    public function we_can_define_show_entity_state()
    {
        $field = SharpShowEntityListField::make('entityListField', 'entityKey')
            ->showEntityState(false);

        $this->assertArraySubset(
            ['showEntityState' => false],
            $field->toArray(),
        );
    }

    /** @test */
    public function we_can_define_show_reorder_button()
    {
        $field = SharpShowEntityListField::make('entityListField', 'entityKey')
            ->showReorderButton(false);

        $this->assertArraySubset(
            ['showReorderButton' => false],
            $field->toArray(),
        );
    }

    /** @test */
    public function we_can_define_show_create_button()
    {
        $field = SharpShowEntityListField::make('entityListField', 'entityKey')
            ->showCreateButton(false);

        $this->assertArraySubset(
            ['showCreateButton' => false],
            $field->toArray(),
        );
    }

    /** @test */
    public function we_can_define_show_search_field()
    {
        $field = SharpShowEntityListField::make('entityListField', 'entityKey')
            ->showSearchField(false);

        $this->assertArraySubset(
            ['showSearchField' => false],
            $field->toArray(),
        );
    }

    /** @test */
    public function we_can_define_show_count()
    {
        $field = SharpShowEntityListField::make('entityListField', 'entityKey')
            ->showCount();

        $this->assertArraySubset(
            ['showCount' => true],
            $field->toArray(),
        );
    }

    /** @test */
    public function we_can_define_hide_entity_commands()
    {
        $field = SharpShowEntityListField::make('entityListField', 'entityKey')
            ->hideEntityCommand(['c1', 'c2']);

        $this->assertArraySubset(
            [
                'hiddenCommands' => [
                    'entity' => [
                        'c1', 'c2',
                    ],
                ],
            ],
            $field->toArray(),
        );

        $field->hideEntityCommand('c3');

        $this->assertArraySubset(
            [
                'hiddenCommands' => [
                    'entity' => [
                        'c1', 'c2', 'c3',
                    ],
                ],
            ],
            $field->toArray(),
        );
    }

    /** @test */
    public function we_can_define_hide_instance_commands()
    {
        $field = SharpShowEntityListField::make('entityListField', 'entityKey')
            ->hideInstanceCommand(['c1', 'c2']);

        $this->assertArraySubset(
            [
                'hiddenCommands' => [
                    'instance' => [
                        'c1', 'c2',
                    ],
                ],
            ],
            $field->toArray(),
        );

        $field->hideInstanceCommand('c3');

        $this->assertArraySubset(
            [
                'hiddenCommands' => [
                    'instance' => [
                        'c1', 'c2', 'c3',
                    ],
                ],
            ],
            $field->toArray(),
        );
    }
}
