<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\Formatters\AutocompleteFormatter;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteField;
use Code16\Sharp\Tests\SharpTestCase;

class AutocompleteFormatterTest extends SharpTestCase
{
    use WithSimpleFormatterTest;

    /** @test */
    function we_can_format_local_value_to_front()
    {
        $value = str_random();

        // Front always need an object
        $this->assertEquals(["id" => $value], (new AutocompleteFormatter)->toFront(
            SharpFormAutocompleteField::make("text", "local"),
            $value
        ));

        $this->assertEquals(["num" => $value], (new AutocompleteFormatter)->toFront(
            SharpFormAutocompleteField::make("text", "local")->setItemIdAttribute("num"),
            $value
        ));
    }

    /** @test */
    function we_can_format_remote_value_to_front()
    {
        $value = [
            "id" => str_random(),
            "label" => str_random()
        ];

        $this->assertEquals($value, (new AutocompleteFormatter)->toFront(
            SharpFormAutocompleteField::make("text", "remote"),
            $value
        ));
    }

    /** @test */
    function we_can_format_local_value_from_front()
    {
        // Front always send an object
        $value = [
            "id" => str_random(),
            "label" => str_random()
        ];

        // Back always need an id
        $this->assertEquals(
            $value["id"],
            (new AutocompleteFormatter)->fromFront(
                SharpFormAutocompleteField::make("text", "local"),
                "attribute",
                $value
            )
        );
    }
}