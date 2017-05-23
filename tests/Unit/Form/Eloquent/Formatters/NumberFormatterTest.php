<?php

namespace Code16\Sharp\Tests\Unit\Form\Eloquent\Formatters;

use Code16\Sharp\Form\Eloquent\Formatters\NumberFormatter;
use Code16\Sharp\Tests\SharpTestCase;

class NumberFormatterTest extends SharpTestCase
{

    /** @test */
    function we_can_format_a_value()
    {
        $formatter = new NumberFormatter;
        $this->assertEquals(10, $formatter->format(10));
        $this->assertEquals(10, $formatter->format("10"));
        $this->assertEquals(0, $formatter->format(""));
        $this->assertEquals(0, $formatter->format("abc"));
    }
}