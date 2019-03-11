<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields;

use Code16\Sharp\Form\Fields\SharpFormWysiwygField;
use Code16\Sharp\Tests\SharpTestCase;

class SharpFormWysiwygFieldTest extends SharpTestCase
{
    /** @test */
    function only_default_values_are_set()
    {
        $formField = SharpFormWysiwygField::make("text");

        $this->assertEquals([
                "key" => "text", "type" => "wysiwyg", "toolbar" => [
                    SharpFormWysiwygField::B, SharpFormWysiwygField::I, SharpFormWysiwygField::SEPARATOR,
                    SharpFormWysiwygField::UL, SharpFormWysiwygField::SEPARATOR, SharpFormWysiwygField::A,
                ]
            ], $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_height()
    {
        $formField = SharpFormWysiwygField::make("text")
            ->setHeight(500);

        $this->assertArrayContainsSubset(
            ["height" => 500],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_toolbar()
    {
        $formField = SharpFormWysiwygField::make("text")
            ->setToolbar([
                SharpFormWysiwygField::B,
                SharpFormWysiwygField::SEPARATOR,
                SharpFormWysiwygField::UL,
            ]);

        $this->assertArrayContainsSubset(
            ["toolbar" => [
                SharpFormWysiwygField::B,
                SharpFormWysiwygField::SEPARATOR,
                SharpFormWysiwygField::UL,
            ]],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_hide_toolbar()
    {
        $formField = SharpFormWysiwygField::make("text")
            ->hideToolbar();

        $this->assertArrayNotHasKey("toolbar", $formField->toArray());
    }

}