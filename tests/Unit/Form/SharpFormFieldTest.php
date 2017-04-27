<?php

namespace Code16\Sharp\Tests\Unit\Form;

use Code16\Sharp\Form\Exceptions\SharpFormFieldValidationException;
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

        $this->assertArraySubset(
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

        $this->assertArraySubset(
            ["label" => "label"],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_helpMessage()
    {
        $formField = SomeTestFormField::make("name")
            ->setHelpMessage("message");

        $this->assertArraySubset(
            ["helpMessage" => "message"],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_conditionalDisplay()
    {
        $formField = SomeTestFormField::make("name")
            ->setConditionalDisplay("display");

        $this->assertArraySubset(
            ["conditionalDisplay" => "display"],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_readOnly()
    {
        $formField = SomeTestFormField::make("name")
            ->setReadOnly();

        $this->assertArraySubset(
            ["readOnly" => true],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_extraStyle()
    {
        $formField = SomeTestFormField::make("name")
            ->setExtraStyle("font-weight: bold");

        $this->assertArraySubset(
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