<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\Formatters\AutocompleteFormatter;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteField;
use Code16\Sharp\Tests\SharpTestCase;
use Illuminate\Support\Str;

class AutocompleteFormatterTest extends SharpTestCase
{
    use WithSimpleFormatterTest;

    /** @test */
    function we_can_format_local_value_to_front()
    {
        $value = Str::random();

        // Front always need an object
        $this->assertEquals(["id" => $value], (new AutocompleteFormatter)->toFront(
            SharpFormAutocompleteField::make("text", "local"),
            $value
        ));

        $this->assertEquals(["num" => $value], (new AutocompleteFormatter)->toFront(
            SharpFormAutocompleteField::make("text", "local")->setItemIdAttribute("num"),
            $value
        ));

        $this->assertEquals(["id" => $value], (new AutocompleteFormatter)->toFront(
            SharpFormAutocompleteField::make("text", "local"),
            ["id" => $value]
        ));

        $this->assertEquals(["id" => $value], (new AutocompleteFormatter)->toFront(
            SharpFormAutocompleteField::make("text", "local"),
            (object)["id" => $value]
        ));

        $this->assertEquals(["id" => $value], (new AutocompleteFormatter)->toFront(
            SharpFormAutocompleteField::make("text", "local"),
            new class($value) {
                function __construct($value) {
                    $this->value = $value;
                }
                function toArray() {
                    return ["id" => $this->value];
                }
            }
        ));
    }

    /** @test */
    function we_can_format_remote_value_to_front()
    {
        $value = [
            "id" => Str::random(),
            "label" => Str::random()
        ];

        $this->assertEquals($value, (new AutocompleteFormatter)->toFront(
            SharpFormAutocompleteField::make("text", "remote"),
            $value
        ));
    }

    /** @test */
    function we_can_format_null_value_to_front()
    {
        $this->assertNull((new AutocompleteFormatter)->toFront(
            SharpFormAutocompleteField::make("text", "local"),
            null
        ));
    }

    /** @test */
    function we_can_format_null_value_from_front()
    {
        $this->assertNull(
            (new AutocompleteFormatter)->fromFront(
                SharpFormAutocompleteField::make("text", "local"),
                "attribute",
                null
            )
        );
    }

    /** @test */
    function we_can_format_local_value_from_front()
    {
        // Front always send an object
        $value = [
            "id" => Str::random(),
            "label" => Str::random()
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