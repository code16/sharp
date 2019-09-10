<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\Formatters\AbstractSimpleFormatter;
use Code16\Sharp\Form\Fields\SharpFormField;
use Illuminate\Support\Str;

trait WithSimpleFormatterTest
{

    function checkSimpleFormatterToFront(SharpFormField $field, AbstractSimpleFormatter $formatter)
    {
        $value = Str::random();

        $this->assertEquals($value, $formatter->toFront($field, $value));
    }

    function checkSimpleFormatterFromFront(SharpFormField $field, AbstractSimpleFormatter $formatter, string $attribute)
    {
        $value = Str::random();

        $this->assertEquals($value, $formatter->fromFront($field, $attribute, $value));
    }
}