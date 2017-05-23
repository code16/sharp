<?php

namespace Code16\Sharp\Tests\Unit\Form\Eloquent\Formatters;

use Code16\Sharp\Form\Eloquent\Formatters\CheckFormatter;
use Code16\Sharp\Tests\SharpTestCase;

class CheckFormatterTest extends SharpTestCase
{

    /** @test */
    function we_can_format_a_true_value()
    {
        $formatter = new CheckFormatter;
        $this->assertTrue($formatter->format(true));
        $this->assertTrue($formatter->format("true"));
        $this->assertTrue($formatter->format(1));
        $this->assertTrue($formatter->format("1"));
        $this->assertTrue($formatter->format("ok"));
        $this->assertTrue($formatter->format("on"));
    }

    /** @test */
    function we_can_format_a_false_value()
    {
        $formatter = new CheckFormatter;
        $this->assertFalse($formatter->format(false));
        $this->assertFalse($formatter->format("false"));
        $this->assertFalse($formatter->format("0"));
        $this->assertFalse($formatter->format(0));
        $this->assertFalse($formatter->format(""));
    }
}