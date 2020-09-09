<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields;

use Code16\Sharp\Form\Fields\SharpFormAutocompleteField;
use Code16\Sharp\Form\Fields\SharpFormMarkdownField;
use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Form\Fields\SharpFormTagsField;
use Code16\Sharp\Form\Fields\SharpFormTextareaField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Fields\SharpFormWysiwygField;
use Code16\Sharp\Tests\SharpTestCase;

class SharpFormFieldWithDataLocalizationTest extends SharpTestCase
{

    /** @test */
    function we_can_define_the_localized_attribute_for_text_field()
    {
        $formField = SharpFormTextField::make("name")
            ->setLocalized(false);

        $this->assertArrayNotHasKey(
            "localized", $formField->toArray()
        );

        $formField->setLocalized();

        $this->assertArraySubset(
            ["localized" => true], $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_the_localized_attribute_for_textarea_field()
    {
        $formField = SharpFormTextareaField::make("name")
            ->setLocalized();

        $this->assertArraySubset(
            ["localized" => true], $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_the_localized_attribute_for_wysiwyg_field()
    {
        $formField = SharpFormWysiwygField::make("name")
            ->setLocalized();

        $this->assertArraySubset(
            ["localized" => true], $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_the_localized_attribute_for_markdown_field()
    {
        $formField = SharpFormMarkdownField::make("name")
            ->setLocalized();

        $this->assertArraySubset(
            ["localized" => true], $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_the_localized_attribute_for_select_field()
    {
        $formField = SharpFormSelectField::make("name", ["1"=>"one"])
            ->setLocalized();

        $this->assertArraySubset(
            ["localized" => true], $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_the_localized_attribute_for_autocomplete_field()
    {
        $formField = SharpFormAutocompleteField::make("name", "local")
            ->setLocalValues(["1" => "one"])
            ->setResultItemInlineTemplate("{{id}}")
            ->setListItemInlineTemplate("{{id}}")
            ->setLocalized();

        $this->assertArraySubset(
            ["localized" => true], $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_the_localized_attribute_for_tags_field()
    {
        $formField = SharpFormTagsField::make("name", ["1"=>"one"])
            ->setLocalized();

        $this->assertArraySubset(
            ["localized" => true], $formField->toArray()
        );
    }
}