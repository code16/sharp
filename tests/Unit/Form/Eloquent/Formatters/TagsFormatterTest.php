<?php

namespace Code16\Sharp\Tests\Unit\Form\Eloquent\Formatters;

use Code16\Sharp\Form\Eloquent\Formatters\TagsFormatter;
use Code16\Sharp\Tests\SharpTestCase;

class TagsFormatterTest extends SharpTestCase
{

    /** @test */
    function we_can_format_a_value()
    {
        $formatter = new TagsFormatter();
        $value = [1,2,3,4];

        $this->assertEquals([1,2,3,4], $formatter->format($value));
    }
}