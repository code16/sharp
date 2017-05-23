<?php

namespace Code16\Sharp\Tests\Unit\Form\Eloquent\Formatters;

use Code16\Sharp\Form\Eloquent\Formatters\AbstractSimpleFormatter;
use Code16\Sharp\Tests\SharpTestCase;

abstract class AbstractSimpleFormatterTest extends SharpTestCase
{

    function checkSimpleFormatter(AbstractSimpleFormatter $formatter)
    {
        $value = str_random();

        $this->assertEquals($value, $formatter->format($value));
    }
}