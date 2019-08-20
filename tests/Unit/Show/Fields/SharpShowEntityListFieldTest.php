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
            "filters" => []
        ], $field->toArray());
    }

    /** @test */
    function we_can_define_show_filers()
    {
        $field = SharpShowEntityListField::make("entityListField", "entityKey")
            ->showFilters(["f1", "f2"]);

        $this->assertEquals([
            "key" => "entityListField",
            "type" => "entityList",
            "entityListKey" => "entityKey",
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

        $this->assertEquals([
            "key" => "entityListField",
            "type" => "entityList",
            "entityListKey" => "entityKey",
            "filters" => [
                "f1" => ["display" => true],
                "f2" => ["display" => true, "value" => "2"],
                "f3" => ["display" => false, "value" => "3"],
            ]
        ], $field->toArray());
    }
}