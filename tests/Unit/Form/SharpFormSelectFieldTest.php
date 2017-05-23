<?php

namespace Code16\Sharp\Tests\Unit\Form;

use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Tests\SharpTestCase;

class SharpFormSelectFieldTest extends SharpTestCase
{
    /** @test */
    function only_default_values_are_set()
    {
        $values = [
            "1" => "Elem 1",
            "2" => "Elem 2"
        ];

        $formField = $this->getDefaultSelect($values);

        $this->assertEquals([
                "key" => "field", "type" => "select",
                "values" => $values, "multiple" => false,
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
     * @param array|null $values
     * @return SharpFormSelectField
     */
    private function getDefaultSelect($values = null)
    {
        return SharpFormSelectField::make("field", $values ?: [
            "1" => "Elem 1",
            "2" => "Elem 2"
        ]);
    }
}