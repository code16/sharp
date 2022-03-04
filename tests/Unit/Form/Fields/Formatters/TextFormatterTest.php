<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\Formatters\TextFormatter;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Tests\SharpTestCase;

class TextFormatterTest extends SharpTestCase
{
    use WithSimpleFormatterTest;

    /** @test */
    public function we_can_format_value_to_front()
    {
        $this->checkSimpleFormatterToFront(SharpFormTextField::make('text'), new TextFormatter());
    }

    /** @test */
    public function we_can_format_value_from_front()
    {
        $this->checkSimpleFormatterFromFront(SharpFormTextField::make('text'), new TextFormatter(), 'attribute');
    }
}
