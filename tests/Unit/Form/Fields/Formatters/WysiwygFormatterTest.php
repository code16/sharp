<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\Formatters\WysiwygFormatter;
use Code16\Sharp\Form\Fields\SharpFormWysiwygField;
use Code16\Sharp\Tests\SharpTestCase;
use Illuminate\Support\Str;

class WysiwygFormatterTest extends SharpTestCase
{

    /** @test */
    function we_can_format_value_to_front()
    {
        $value = Str::random();

        $this->assertEquals(["text" => $value], (new WysiwygFormatter())
            ->toFront(SharpFormWysiwygField::make("a"), $value));
    }

    /** @test */
    function we_can_format_value_from_front()
    {
        $value = Str::random();

        $this->assertEquals(
            $value,
            (new WysiwygFormatter())->fromFront(SharpFormWysiwygField::make("a"), "attribute", ["text" => $value])
        );
    }
}