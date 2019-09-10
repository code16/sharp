<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\Formatters\ListFormatter;
use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Tests\SharpTestCase;
use Illuminate\Support\Arr;

class ListFormatterTest extends SharpTestCase
{

    /** @test */
    function we_can_format_value_to_front()
    {
        $formatter = new ListFormatter;
        $field = SharpFormListField::make("list")
            ->addItemField(SharpFormTextField::make("name"))
            ->addItemField(SharpFormTextField::make("job"));

        $data = $this->getData();

        $this->assertEquals($data, $formatter->toFront($field, $data));
    }

    /** @test */
    function non_configured_values_are_ignored_when_formatting_to_front()
    {
        $formatter = new ListFormatter;
        $field = SharpFormListField::make("list")
            ->addItemField(SharpFormTextField::make("name"));

        $expectedData = collect($this->getData())->map(function($item) {
            return Arr::except($item, "job");
        })->all();

        $this->assertEquals($expectedData, $formatter->toFront($field, $this->getData()));
    }

    /** @test */
    function we_can_format_value_from_front()
    {
        $formatter = new ListFormatter;
        $attribute = "attribute";
        $field = SharpFormListField::make("list")
            ->addItemField(SharpFormTextField::make("name"))
            ->addItemField(SharpFormTextField::make("job"));

        $data = $this->getData();

        $this->assertEquals($data, $formatter->fromFront($field, $attribute, $data));
    }

    /** @test */
    function non_configured_values_are_ignored_when_formatting_from_front()
    {
        $formatter = new ListFormatter;
        $attribute = "attribute";
        $field = SharpFormListField::make("list")
            ->addItemField(SharpFormTextField::make("name"));

        $expectedData = collect($this->getData())->map(function($item) {
            return Arr::except($item, "job");
        })->all();

        $this->assertEquals($expectedData, $formatter->fromFront($field, $attribute, $this->getData()));
    }

    /** @test */
    function we_can_configure_the_id_attribute()
    {
        $formatter = new ListFormatter;
        $field = SharpFormListField::make("list")
            ->setItemIdAttribute("number")
            ->addItemField(SharpFormTextField::make("name"))
            ->addItemField(SharpFormTextField::make("job"));

        $data = collect($this->getData())->map(function($item) {
            return array_merge([
                "number" => $item["id"],
            ], Arr::except($item, "id"));
        })->all();

        $this->assertEquals($data, $formatter->toFront($field, $data));
    }

    /** @test */
    function non_valuated_values_are_initialized_to_null_when_formatting_to_front()
    {
        $formatter = new ListFormatter;
        $field = SharpFormListField::make("list")
            ->addItemField(SharpFormTextField::make("name"))
            ->addItemField(SharpFormTextField::make("job"));

        $this->assertEquals([
            ["id" => 1, "name" => "John Wayne", "job" => null]
        ], $formatter->toFront($field, [
            ["id" => 1, "name" => "John Wayne"]
        ]));
    }

    /**
     * @return array
     */
    protected function getData(): array
    {
        return [
            [
                "id" => 1,
                "name" => "John Wayne",
                "job" => "Actor"
            ], [
                "id" => 2,
                "name" => "John Ford",
                "job" => "Director"
            ]
        ];
    }
}