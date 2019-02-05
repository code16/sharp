<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields;

use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Tests\SharpTestCase;

class SharpFormSelectFieldTest extends SharpTestCase
{
    /** @test */
    function only_default_values_are_set()
    {
        $options = [
            "1" => "Elem 1",
            "2" => "Elem 2"
        ];

        $formField = $this->getDefaultSelect($options);

        $this->assertEquals([
                "key" => "field", "type" => "select",
                "options" => [
                    ["id" => "1", "label" => "Elem 1"],
                    ["id" => "2", "label" => "Elem 2"],
                ], "multiple" => false,
                "clearable" => false, "inline" => false, "display" => "list",
            ], $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_multiple()
    {
        $formField = $this->getDefaultSelect()
            ->setMultiple(true);

        $this->assertArraySubset(
            ["multiple" => true],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_inline()
    {
        $formField = $this->getDefaultSelect()
            ->setInline();

        $this->assertArraySubset(
            ["inline" => true],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_maxSelected()
    {
        $formField = $this->getDefaultSelect()
            ->setMaxSelected(12);

        $this->assertArraySubset(
            ["maxSelected" => 12],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_clearable()
    {
        $formField = $this->getDefaultSelect()
            ->setClearable(true);

        $this->assertArraySubset(
            ["clearable" => true],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_display_as_list()
    {
        $formField = $this->getDefaultSelect()
            ->setDisplayAsList();

        $this->assertArraySubset(
            ["display" => "list"],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_display_as_dropdown()
    {
        $formField = $this->getDefaultSelect()
            ->setDisplayAsDropdown();

        $this->assertArraySubset(
            ["display" => "dropdown"],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_options_as_a_id_label_array()
    {
        $formField = $this->getDefaultSelect([
            ["id" => 1, "label" => "Elem 1"],
            ["id" => 2, "label" => "Elem 2"],
        ]);

        $this->assertArraySubset(
            ["options" => [
                ["id" => 1, "label" => "Elem 1"],
                ["id" => 2, "label" => "Elem 2"],
            ]],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_localized_options()
    {
        $formField = $this->getDefaultSelect([
            "1" => ["en" => "Option one", "fr" => "Option un"],
            "2" => ["en" => "Option two", "fr" => "Option deux"],
        ])->setLocalized();

        $this->assertArraySubset(
            ["options" => [
                ["id" => 1, "label" => ["en" => "Option one", "fr" => "Option un"]],
                ["id" => 2, "label" => ["en" => "Option two", "fr" => "Option deux"]],
            ]],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_localized_options_with_id_label_array()
    {
        $formField = $this->getDefaultSelect([
            ["id" => "1", "label" => ["en" => "Option one", "fr" => "Option un"]],
            ["id" => "2", "label" => ["en" => "Option two", "fr" => "Option deux"]],
        ])->setLocalized();

        $this->assertArraySubset(
            ["options" => [
                ["id" => 1, "label" => ["en" => "Option one", "fr" => "Option un"]],
                ["id" => 2, "label" => ["en" => "Option two", "fr" => "Option deux"]],
            ]],
            $formField->toArray()
        );
    }

    /**
     * @param array|null $options
     * @return SharpFormSelectField
     */
    private function getDefaultSelect($options = null)
    {
        return SharpFormSelectField::make("field", $options ?: [
            "1" => "Elem 1",
            "2" => "Elem 2"
        ]);
    }
}