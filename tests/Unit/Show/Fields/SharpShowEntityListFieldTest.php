<?php

namespace Code16\Sharp\Tests\Unit\Show\Fields;

use Code16\Sharp\Show\Fields\SharpShowEntityListField;
use Code16\Sharp\Tests\SharpTestCase;

class SharpShowEntityListFieldTest extends SharpTestCase
{
    /** @test */
    function we_can_define_entity_list_field()
    {
        $field = SharpShowEntityListField::make("entityListField", "entityKey");

        $this->assertEquals([
            "key" => "entityListField",
            "type" => "entityList",
            "entityListKey" => "entityKey",
            "showEntityState" => true,
            "showCreateButton" => true,
            "showReorderButton" => true,
            "showSearchField" => true,
            "emptyVisible" => false,
            "hiddenFilters" => [],
            "hiddenCommands" => ["entity" => [], "instance" => []],
        ], $field->toArray());
    }

    /** @test */
    function we_can_define_hideFilterWithValue()
    {
        $field = SharpShowEntityListField::make("entityListField", "entityKey")
            ->hideFilterWithValue("f1", "value1");

        $this->assertArrayContainsSubset([
            "hiddenFilters" => [
                "f1" => "value1",
            ]
        ], $field->toArray());
    }

    /** @test */
    function we_can_define_hideFilterWithValue_with_a_callable()
    {
        $field = SharpShowEntityListField::make("entityListField", "entityKey")
            ->hideFilterWithValue("f1", function($instanceId) {
                return "computed";
            });

        $this->assertArrayContainsSubset([
            "hiddenFilters" => [
                "f1" => "computed",
            ]
        ], $field->toArray());
    }

    /** @test */
    function we_can_define_showEntityState()
    {
        $field = SharpShowEntityListField::make("entityListField", "entityKey")
            ->showEntityState(false);

        $this->assertArrayContainsSubset([
            "showEntityState" => false
        ], $field->toArray());
    }

    /** @test */
    function we_can_define_showReorderButton()
    {
        $field = SharpShowEntityListField::make("entityListField", "entityKey")
            ->showReorderButton(false);

        $this->assertArrayContainsSubset([
            "showReorderButton" => false
        ], $field->toArray());
    }

    /** @test */
    function we_can_define_showCreateButton()
    {
        $field = SharpShowEntityListField::make("entityListField", "entityKey")
            ->showCreateButton(false);

        $this->assertArrayContainsSubset([
            "showCreateButton" => false
        ], $field->toArray());
    }

    /** @test */
    function we_can_define_showSearchField()
    {
        $field = SharpShowEntityListField::make("entityListField", "entityKey")
            ->showSearchField(false);

        $this->assertArrayContainsSubset([
            "showSearchField" => false
        ], $field->toArray());
    }


    /** @test */
    function we_can_define_hideEntityCommands()
    {
        $field = SharpShowEntityListField::make("entityListField", "entityKey")
            ->hideEntityCommand(["c1", "c2"]);

        $this->assertArrayContainsSubset([
            "hiddenCommands" => [
                "entity" => [
                    "c1", "c2"
                ]
            ]
        ], $field->toArray());

        $field->hideEntityCommand("c3");

        $this->assertArrayContainsSubset([
            "hiddenCommands" => [
                "entity" => [
                    "c1", "c2", "c3"
                ]
            ]
        ], $field->toArray());
    }

    /** @test */
    function we_can_define_hideInstanceCommands()
    {
        $field = SharpShowEntityListField::make("entityListField", "entityKey")
            ->hideInstanceCommand(["c1", "c2"]);

        $this->assertArrayContainsSubset([
            "hiddenCommands" => [
                "instance" => [
                    "c1", "c2"
                ]
            ]
        ], $field->toArray());

        $field->hideInstanceCommand("c3");

        $this->assertArrayContainsSubset([
            "hiddenCommands" => [
                "instance" => [
                    "c1", "c2", "c3"
                ]
            ]
        ], $field->toArray());
    }
}