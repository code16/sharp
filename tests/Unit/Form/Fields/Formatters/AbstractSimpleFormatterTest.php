<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\Formatters\AbstractSimpleFormatter;
use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Tests\SharpTestCase;

abstract class AbstractSimpleFormatterTest extends SharpTestCase
{

    function checkSimpleFormatterToFront(SharpFormField $field, AbstractSimpleFormatter $formatter)
    {
        $value = str_random();

        $this->assertEquals($value, $formatter->toFront($field, $value));
    }

    function checkSimpleFormatterFromFront(SharpFormField $field, AbstractSimpleFormatter $formatter, string $attribute)
    {
        $value = str_random();

        $this->assertEquals($value, $formatter->fromFront($field, $attribute, $value));
    }
}