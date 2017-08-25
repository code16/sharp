<?php

namespace Code16\Sharp\Tests\Unit\Form\Eloquent\Formatters;

use Code16\Sharp\Form\Eloquent\Formatters\TagsFormatter;
use Code16\Sharp\Form\Fields\SharpFormTagsField;
use Code16\Sharp\Tests\SharpTestCase;

class TagsFormatterTest extends SharpTestCase
{

    /** @test */
    function we_can_format_a_value()
    {
        $formatter = new TagsFormatter();

        $field = SharpFormTagsField::make("tags", [
            1=>"A", 2=>"B"
        ]);

        $value = [
            ["id" => 1],
            ["id" => 2]
        ];

        $this->assertEquals($value, $formatter->format($value, $field));
    }

    /** @test */
    function we_can_format_a_value_with_creatable()
    {
        $formatter = new TagsFormatter();

        $field = SharpFormTagsField::make("tags", [
            1=>"A"
        ])->setCreatable()->setLabelAttribute("name");

        $value = [
            ["id" => 1],
            ["id" => null, "label" => "B"]
        ];

        $this->assertEquals([
            ["id" => 1],
            ["id" => null, "name" => "B"]
        ], $formatter->format($value, $field));
    }

    /** @test */
    function created_values_are_stripped_if_not_creatable()
    {
        $formatter = new TagsFormatter();

        $field = SharpFormTagsField::make("tags", [
            1=>"A"
        ])->setCreatable(false);

        $value = [
            ["id" => 1],
            ["id" => null, "label" => "B"]
        ];

        $this->assertEquals([
            ["id" => 1],
        ], $formatter->format($value, $field));
    }
}