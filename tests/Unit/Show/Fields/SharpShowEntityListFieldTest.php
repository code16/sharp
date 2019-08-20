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
            "showEntityState" => false,
            "showCreateButton" => false,
            "showReorderButton" => false,
            "filters" => [],
            "commands" => ["entity" => [], "instance" => []],
        ], $field->toArray());
    }

    /** @test */
    function we_can_define_show_filers()
    {
        $field = SharpShowEntityListField::make("entityListField", "entityKey")
            ->showFilters(["f1", "f2"]);

        $this->assertArrayContainsSubset([
            "filters" => [
                "f1" => ["display" => true],
                "f2" => ["display" => true],
            ]
        ], $field->toArray());
    }

    /** @test */
    function we_can_define_filer_values()
    {
        $field = SharpShowEntityListField::make("entityListField", "entityKey")
            ->showFilters(["f1", "f2"])
            ->setFilterValue("f2", "2")
            ->setFilterValue("f3", "3");

        $this->assertArrayContainsSubset([
            "filters" => [
                "f1" => ["display" => true],
                "f2" => ["display" => true, "value" => "2"],
                "f3" => ["display" => false, "value" => "3"],
            ]
        ], $field->toArray());
    }

    /** @test */
    function we_can_define_showEntityState()
    {
        $field = SharpShowEntityListField::make("entityListField", "entityKey")
            ->showEntityState();

        $this->assertArrayContainsSubset([
            "showEntityState" => true
        ], $field->toArray());
    }

    /** @test */
    function we_can_define_showReorderButton()
    {
        $field = SharpShowEntityListField::make("entityListField", "entityKey")
            ->showReorderButton();

        $this->assertArrayContainsSubset([
            "showReorderButton" => true
        ], $field->toArray());
    }

    /** @test */
    function we_can_define_showCreateButton()
    {
        $field = SharpShowEntityListField::make("entityListField", "entityKey")
            ->showCreateButton();

        $this->assertArrayContainsSubset([
            "showCreateButton" => true
        ], $field->toArray());
    }

    /** @test */
    function we_can_define_showEntityCommands()
    {
        $field = SharpShowEntityListField::make("entityListField", "entityKey")
            ->showEntityCommands(["c1", "c2"]);

        $this->assertArrayContainsSubset([
            "commands" => [
                "entity" => [
                    "c1" => ["display"=>true],
                    "c2" => ["display"=>true],
                ]
            ]
        ], $field->toArray());
    }

    /** @test */
    function we_can_define_showInstanceCommands()
    {
        $field = SharpShowEntityListField::make("entityListField", "entityKey")
            ->showInstanceCommands(["c1", "c2"]);

        $this->assertArrayContainsSubset([
            "commands" => [
                "instance" => [
                    "c1" => ["display"=>true],
                    "c2" => ["display"=>true],
                ]
            ]
        ], $field->toArray());
    }
}