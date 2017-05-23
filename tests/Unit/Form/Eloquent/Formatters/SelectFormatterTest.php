<?php

namespace Code16\Sharp\Tests\Unit\Form\Eloquent\Formatters;

use Code16\Sharp\Form\Eloquent\Formatters\SelectFormatter;
use Code16\Sharp\Tests\SharpTestCase;

class SelectFormatterTest extends SharpTestCase
{

    /** @test */
    function we_can_format_a_single_value()
    {
        $formatter = new SelectFormatter;
        $value = str_random();

        $this->assertEquals($value, $formatter->format($value));
    }

    /** @test */
    function we_can_format_a_multiple_value()
    {
        $formatter = new SelectFormatter;
        $value = [1,2,3,4];

        $this->assertEquals([1,2,3,4], $formatter->format($value));
    }
}