<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\Formatters\MarkdownFormatter;

class MarkdownFormatterTest extends AbstractSimpleFormatterTest
{

    /** @test */
    function we_can_format_value()
    {
        $this->checkSimpleFormatter(new MarkdownFormatter);
    }
}