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

        $this->assertEquals(
            [
                "key" => "text", 
                "type" => "wysiwyg", 
                "toolbar" => [
                    SharpFormWysiwygField::B, SharpFormWysiwygField::I, SharpFormWysiwygField::SEPARATOR,
                    SharpFormWysiwygField::UL, SharpFormWysiwygField::SEPARATOR, SharpFormWysiwygField::A,
                ],
                "minHeight" => 200,
                "innerComponents" => [
                    "upload" => [
                        "maxFileSize" => 2,
                        "transformable" => true,
                        "fileFilter" => [".jpg",".jpeg",".gif",".png"]
                    ]
                ]
            ], 
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_height()
    {
        $formField = SharpFormWysiwygField::make("text")
            ->setHeight(50);

        $this->assertArraySubset(
            ["minHeight" => 50, "maxHeight" => 50],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_height_with_maxHeight()
    {
        $formField = SharpFormWysiwygField::make("text");

        $this->assertArraySubset(
            ["minHeight" => 50, "maxHeight" => 100],
            $formField->setHeight(50, 100)->toArray()
        );

        $this->assertArraySubset(
            ["minHeight" => 50],
            $formField->setHeight(50, 0)->toArray()
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

        $this->assertArraySubset(
            [
                "toolbar" => [
                    SharpFormWysiwygField::B,
                    SharpFormWysiwygField::SEPARATOR,
                    SharpFormWysiwygField::UL,
                ]
            ],
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