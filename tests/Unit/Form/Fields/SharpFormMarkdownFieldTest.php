<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields;

use Code16\Sharp\Form\Fields\SharpFormMarkdownField;
use Code16\Sharp\Tests\SharpTestCase;

class SharpFormMarkdownFieldTest extends SharpTestCase
{
    /** @test */
    function only_default_values_are_set()
    {
        $formField = SharpFormMarkdownField::make("text");

        $this->assertEquals(
            [
                "key" => "text", 
                "type" => "markdown",
                "minHeight" => 200,
                "toolbar" => [
                    SharpFormMarkdownField::B, SharpFormMarkdownField::I, SharpFormMarkdownField::SEPARATOR,
                    SharpFormMarkdownField::UL, SharpFormMarkdownField::SEPARATOR, SharpFormMarkdownField::A,
                ], 
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
        $formField = SharpFormMarkdownField::make("text")
            ->setHeight(50);

        $this->assertArraySubset(
            ["minHeight" => 50, "maxHeight" => 50],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_height_with_maxHeight()
    {
        $formField = SharpFormMarkdownField::make("text");

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
    function we_can_define_upload_configuration()
    {
        $formField = SharpFormMarkdownField::make("text")
            ->setMaxFileSize(50);

        $this->assertArraySubset(
            [
                "innerComponents" => [
                    "upload" => [
                        "maxFileSize" => 50,
                        "transformable" => true,
                    ]
                ]
            ], 
            $formField->toArray()
        );

        $formField->setCropRatio("16:9");

        $this->assertArraySubset(
            [
                "innerComponents" => [
                    "upload" => [
                        "maxFileSize" => 50,
                        "transformable" => true,
                        "ratioX" => 16,
                        "ratioY" => 9
                    ]
                ]
            ], 
            $formField->toArray()
        );
        
        $formField->setFileFilter(["jpg", "pdf"]);

        $this->assertArraySubset(
            [
                "innerComponents" => [
                    "upload" => [
                        "maxFileSize" => 50,
                        "ratioX" => 16,
                        "ratioY" => 9,
                        "transformable" => true,
                        "fileFilter" => [".jpg", ".pdf"]
                    ]
                ]
            ],
            $formField->toArray()
        );

        $formField->setTransformable(false);

        $this->assertArraySubset(
            [
                "innerComponents" => [
                    "upload" => [
                        "maxFileSize" => 50,
                        "ratioX" => 16,
                        "ratioY" => 9,
                        "transformable" => false,
                        "fileFilter" => [".jpg", ".pdf"]
                    ]
                ]
            ],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_toolbar()
    {
        $formField = SharpFormMarkdownField::make("text")
            ->setToolbar([
                SharpFormMarkdownField::UPLOAD,
                SharpFormMarkdownField::SEPARATOR,
                SharpFormMarkdownField::UL,
            ]);

        $this->assertArraySubset(
            ["toolbar" => [
                SharpFormMarkdownField::UPLOAD,
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