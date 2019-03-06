<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields;

use Code16\Sharp\Form\Fields\SharpFormCheckField;
use Code16\Sharp\Tests\SharpTestCase;

class SharpFormCheckFieldTest extends SharpTestCase
{
    /** @test */
    function only_default_values_are_set()
    {
        $formField = SharpFormCheckField::make("check", "text");

        $this->assertEquals([
                "key" => "check", "type" => "check",
                "text" => "text"
            ], $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_text()
    {
        $formField = SharpFormCheckField::make("check", "text")
            ->setText("text 2");

        $this->assertArrayContainsSubset(
            ["text" => "text 2"],
            $formField->toArray()
        );
    }
}