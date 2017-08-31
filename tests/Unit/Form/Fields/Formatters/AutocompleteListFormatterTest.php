<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\Formatters\AutocompleteListFormatter;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteField;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteListField;
use Code16\Sharp\Tests\SharpTestCase;

class AutocompleteListFormatterTest extends SharpTestCase
{

    /** @test */
    function we_can_format_value_to_front()
    {
        $formatter = new AutocompleteListFormatter();
        $field = SharpFormAutocompleteListField::make("list")
            ->setItemField(SharpFormAutocompleteField::make("item", "remote")
                ->setRemoteEndpoint("/endpoint")
            );

        $expectedData = collect($this->getData())->map(function($item) {
            return [
                "id" => $item["id"],
                "item" => $item
            ];
        })->all();

        $this->assertEquals($expectedData, $formatter->toFront($field, $this->getData()));
    }

    /** @test */
    function we_can_format_value_from_front()
    {
        $formatter = new AutocompleteListFormatter();
        $field = SharpFormAutocompleteListField::make("list")
            ->setItemField(SharpFormAutocompleteField::make("item", "remote")
                ->setRemoteEndpoint("/endpoint")
            );

        $expectedData = collect($this->getData())->map(function($item) {
            return [
                "id" => $item["item"]
            ];
        })->all();

        $this->assertEquals($expectedData, $formatter->fromFront($field, 'attribute', $this->getData()));
    }

    /**
     * @return array
     */
    protected function getData()
    {
        return [
            [
                "id" => 1,
                "item" => "A",
            ], [
                "id" => 2,
                "item" => "B",
            ]
        ];
    }
}