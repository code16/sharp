<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\Formatters\NumberFormatter;
use Code16\Sharp\Form\Fields\SharpFormNumberField;
use Code16\Sharp\Tests\SharpTestCase;

class NumberFormatterTest extends SharpTestCase
{

    /** @test */
    function we_can_format_value_to_front()
    {
        $formatter = new NumberFormatter;
        $field = SharpFormNumberField::make("number");

        $this->assertEquals(10, $formatter->toFront($field, 10));
        $this->assertEquals(10, $formatter->toFront($field, "10"));
        $this->assertEquals(0, $formatter->toFront($field, ""));
        $this->assertEquals(0, $formatter->toFront($field, "abc"));
    }

    /** @test */
    function we_can_format_value_from_front()
    {
        $formatter = new NumberFormatter;
        $attribute = "attribute";
        $field = SharpFormNumberField::make("number");

        $this->assertEquals(10, $formatter->fromFront($field, $attribute, 10));
        $this->assertEquals(10, $formatter->fromFront($field, $attribute, "10"));
        $this->assertEquals(0, $formatter->fromFront($field, $attribute, ""));
        $this->assertEquals(0, $formatter->fromFront($field, $attribute, "abc"));
    }
}