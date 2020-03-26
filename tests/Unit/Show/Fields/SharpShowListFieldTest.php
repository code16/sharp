<?php

namespace Code16\Sharp\Tests\Unit\Show\Fields;

use Code16\Sharp\Show\Fields\SharpShowFileField;
use Code16\Sharp\Show\Fields\SharpShowListField;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Tests\SharpTestCase;

class SharpShowListFieldTest extends SharpTestCase
{
    /** @test */
    function we_can_define_a_list_field()
    {
        $field = SharpShowListField::make("listField")
            ->addItemField(SharpShowTextField::make("textField"))
            ->setLabel("test");

        $this->assertEquals([
            "key" => "listField",
            "type" => "list",
            "label" => "test",
            "itemFields" => [
                "textField" => [
                    "key" => "textField",
                    "type" => "text",
                ]
            ],
        ], $field->toArray());
    }
}