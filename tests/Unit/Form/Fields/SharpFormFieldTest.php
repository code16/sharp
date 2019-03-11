<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields;

use Code16\Sharp\Exceptions\Form\SharpFormFieldValidationException;
use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Tests\SharpTestCase;

class SharpFormFieldTest extends SharpTestCase
{
    /** @test */
    function we_must_define_a_key()
    {
        $this->expectException(SharpFormFieldValidationException::class);
        SomeTestFormField::make("")->toArray();
    }

    /** @test */
    function we_must_define_a_type()
    {
        $this->expectException(SharpFormFieldValidationException::class);
        SomeTestFormField::make("name", "")->toArray();
    }

    /** @test */
    function returned_array_contains_key_and_type()
    {
        $formField = SomeTestFormField::make("name", "test");

        $this->assertArrayContainsSubset(
            ["key" => "name", "type" => "test"],
            $formField->toArray()
        );
    }

    /** @test */
    function returned_array_does_not_contain_null_attributes()
    {
        $formField = SomeTestFormField::make("name");

        $this->assertEquals(
            ["key" => "name", "type" => "test"],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_label()
    {
        $formField = SomeTestFormField::make("name")
            ->setLabel("label");

        $this->assertArrayContainsSubset(
            ["label" => "label"],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_helpMessage()
    {
        $formField = SomeTestFormField::make("name")
            ->setHelpMessage("message");

        $this->assertArrayContainsSubset(
            ["helpMessage" => "message"],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_conditionalDisplay()
    {
        $formField = SomeTestFormField::make("name")
            ->setConditionalDisplayOrOperator()
            ->addConditionalDisplay("is_displayed")
            ->addConditionalDisplay("color", ["blue", "red"])
            ->addConditionalDisplay("size", "!xl")
            ->addConditionalDisplay("!hidden")
            ->addConditionalDisplay("really_hidden", false);

        $this->assertArrayContainsSubset([
            "conditionalDisplay" => [
                "operator" => "or",
                "fields" => [
                    [
                        "key" => "is_displayed",
                        "values" => true
                    ], [
                        "key" => "color",
                        "values" => ["blue", "red"]
                    ], [
                        "key" => "size",
                        "values" => "!xl"
                    ], [
                        "key" => "hidden",
                        "values" => false
                    ], [
                        "key" => "really_hidden",
                        "values" => false
                    ]
                ]
            ]
        ], $formField->toArray());
    }

    /** @test */
    function we_can_define_readOnly()
    {
        $formField = SomeTestFormField::make("name")
            ->setReadOnly();

        $this->assertArrayContainsSubset(
            ["readOnly" => true],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_extraStyle()
    {
        $formField = SomeTestFormField::make("name")
            ->setExtraStyle("font-weight: bold");

        $this->assertArrayContainsSubset(
            ["extraStyle" => "font-weight: bold"],
            $formField->toArray()
        );
    }

}

class SomeTestFormField extends SharpFormField
{
    public static function make(string $key, $type = "test")
    {
        return new static($key, $type);
    }

    public function toArray(): array
    {
        return parent::buildArray([]);
    }
}