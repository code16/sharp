<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields;

use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Tests\SharpTestCase;

class SharpFormTextFieldTest extends SharpTestCase
{
    /** @test */
    function we_can_define_inputType()
    {
        $defaultFormField = SharpFormTextField::make("name");

        $textFormField = SharpFormTextField::make("name")
            ->setInputTypeText();

        $passwordFormField = SharpFormTextField::make("name")
            ->setInputTypePassword();

        $this->assertArrayContainsSubset(
            ["key" => "name", "type" => "text", "inputType" => "text"],
            $defaultFormField->toArray()
        );

        $this->assertArrayContainsSubset(
            ["key" => "name", "type" => "text", "inputType" => "text"],
            $textFormField->toArray()
        );

        $this->assertArrayContainsSubset(
            ["key" => "name", "type" => "text", "inputType" => "password"],
            $passwordFormField->toArray()
        );
    }

    /** @test */
    function we_can_define_placeholder()
    {
        $formField = SharpFormTextField::make("name")
            ->setPlaceholder("placeholder");

        $this->assertArrayContainsSubset(
            ["key" => "name", "type" => "text", "placeholder" => "placeholder"],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_maxLength()
    {
        $formField = SharpFormTextField::make("text")
            ->setMaxLength(10);

        $this->assertArrayContainsSubset(
            ["maxLength" => 10],
            $formField->toArray()
        );
    }
}