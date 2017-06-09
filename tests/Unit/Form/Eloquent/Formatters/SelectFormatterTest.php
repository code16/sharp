<?php

namespace Code16\Sharp\Tests\Unit\Form\Eloquent\Formatters;

use Code16\Sharp\Form\Eloquent\Formatters\SelectFormatter;
use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Tests\SharpTestCase;

class SelectFormatterTest extends SharpTestCase
{

    /** @test */
    function we_can_format_a_single_value()
    {
        $formatter = new SelectFormatter;
        $field = SharpFormSelectField::make("select", [1=>"A", 2=>"B"]);

        $this->assertEquals(1, $formatter->format(1, $field));
    }

    /** @test */
    function we_can_format_a_multiple_value()
    {
        $formatter = new SelectFormatter;
        $field = SharpFormSelectField::make("select", [1=>"A", 2=>"B"])
            ->setMultiple();

        $this->assertEquals([["id"=>1], ["id"=>2]], $formatter->format([1,2], $field));
        $this->assertEquals([["id"=>1]], $formatter->format(1, $field));
    }

    /** @test */
    function we_strip_values_if_not_multiple()
    {
        $formatter = new SelectFormatter;
        $field = SharpFormSelectField::make("select", [1=>"A", 2=>"B"])
            ->setMultiple(false);

        $this->assertEquals(1, $formatter->format([1,2], $field));
    }
}