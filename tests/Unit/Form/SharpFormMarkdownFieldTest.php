<?php

namespace Code16\Sharp\Tests\Unit\Form;

use Code16\Sharp\Form\Fields\SharpFormMarkdownField;
use Code16\Sharp\Tests\SharpTestCase;

class SharpFormMarkdownFieldTest extends SharpTestCase
{
    /** @test */
    function only_default_values_are_set()
    {
        $formField = SharpFormMarkdownField::make("text");

        $this->assertEquals([
                "key" => "text", "type" => "markdown"
            ], $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_height()
    {
        $formField = SharpFormMarkdownField::make("text")
            ->setHeight(50);

        $this->assertArraySubset(
            ["height" => 50],
            $formField->toArray()
        );
    }
}