<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\Formatters\AutocompleteFormatter;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteField;

class AutocompleteFormatterTest extends AbstractSimpleFormatterTest
{

    /** @test */
    function we_can_format_value_to_front()
    {
        $this->checkSimpleFormatterToFront(SharpFormAutocompleteField::make("text", "local"), new AutocompleteFormatter);
    }

    /** @test */
    function we_can_format_value_from_front()
    {
        $this->checkSimpleFormatterFromFront(SharpFormAutocompleteField::make("text", "local"), new AutocompleteFormatter, "attribute");
    }
}