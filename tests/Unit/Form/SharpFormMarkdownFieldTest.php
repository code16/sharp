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
                "key" => "text", "type" => "markdown", "toolbar" => [
                    SharpFormMarkdownField::B, SharpFormMarkdownField::I, SharpFormMarkdownField::SEPARATOR,
                    SharpFormMarkdownField::UL, SharpFormMarkdownField::SEPARATOR, SharpFormMarkdownField::A,
                ], "maxImageSize" => 2
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

    /** @test */
    function we_can_define_maxFileSize()
    {
        $formField = SharpFormMarkdownField::make("text")
            ->setMaxFileSize(50);

        $this->assertArraySubset(
            ["maxImageSize" => 50],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_toolbar()
    {
        $formField = SharpFormMarkdownField::make("text")
            ->setToolbar([
                SharpFormMarkdownField::IMG,
                SharpFormMarkdownField::SEPARATOR,
                SharpFormMarkdownField::UL,
            ]);

        $this->assertArraySubset(
            ["toolbar" => [
                SharpFormMarkdownField::IMG,
                SharpFormMarkdownField::SEPARATOR,
                SharpFormMarkdownField::UL,
            ]],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_hide_toolbar()
    {
        $formField = SharpFormMarkdownField::make("text")
            ->setHeight(50)
            ->hideToolbar();

        $this->assertArrayNotHasKey("toolbar", $formField->toArray());
    }
}