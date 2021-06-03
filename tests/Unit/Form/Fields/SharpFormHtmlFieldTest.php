<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields;

use Code16\Sharp\Exceptions\Form\SharpFormFieldValidationException;
use Code16\Sharp\Form\Fields\SharpFormHtmlField;
use Code16\Sharp\Tests\SharpTestCase;

class SharpFormHtmlFieldTest extends SharpTestCase
{
    /** @test */
    function we_can_define_inline_template()
    {
        $defaultFormField = SharpFormHtmlField::make("html")
            ->setInlineTemplate("<b>test</b>");

        $this->assertEquals(
            [
                "key" => "html", 
                "type" => "html",
                "template" => "<b>test</b>"
            ], 
            $defaultFormField->toArray()
        );
    }

    /** @test */
    function inline_template_is_mandatory()
    {
        $defaultFormField = SharpFormHtmlField::make("html");

        $this->expectException(SharpFormFieldValidationException::class);
        $defaultFormField->toArray();
    }

    /** @test */
    function we_can_define_templateData()
    {
        $formField = SharpFormHtmlField::make("html")
            ->setInlineTemplate("<b>test</b>")
            ->setAdditionalTemplateData([
                "lang" => ["fr", "de"]
            ]);

        $this->assertArraySubset(
            [
                "templateData" => [
                    "lang" => ["fr", "de"]
                ]
            ],
            $formField->toArray()
        );
    }
}