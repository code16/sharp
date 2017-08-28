<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\Formatters\TextareaFormatter;
use Code16\Sharp\Form\Fields\SharpFormTextareaField;

class TextareaFormatterTest extends AbstractSimpleFormatterTest
{

    /** @test */
    function we_can_format_value_to_front()
    {
        $this->checkSimpleFormatterToFront(SharpFormTextareaField::make("text"), new TextareaFormatter);
    }

    /** @test */
    function we_can_format_value_from_front()
    {
        $this->checkSimpleFormatterFromFront(SharpFormTextareaField::make("text"), new TextareaFormatter, "attribute");
    }
}