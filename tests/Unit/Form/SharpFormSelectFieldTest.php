<?php

namespace Code16\Sharp\Tests\Unit\Form;

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
                "clearable" => false, "display" => "list"
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