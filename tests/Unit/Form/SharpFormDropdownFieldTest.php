<?php

namespace Code16\Sharp\Tests\Unit\Form;

use Code16\Sharp\Form\Fields\SharpFormDropdownField;
use Code16\Sharp\Tests\SharpTestCase;

class SharpFormDropdownFieldTest extends SharpTestCase
{
    /** @test */
    function only_default_values_are_set()
    {
        $values = [
            "1" => "Elem 1",
            "2" => "Elem 2"
        ];

        $formField = $this->getDefaultDropdown($values);

        $this->assertEquals([
                "key" => "field", "type" => "dropdown",
                "values" => $values, "multiple" => false,
                "clearable" => false
            ], $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_multiple()
    {
        $formField = $this->getDefaultDropdown()
            ->setMultiple(true);

        $this->assertArraySubset(
            ["multiple" => true],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_clearable()
    {
        $formField = $this->getDefaultDropdown()
            ->setClearable(true);

        $this->assertArraySubset(
            ["clearable" => true],
            $formField->toArray()
        );
    }

    /**
     * @param array|null $values
     * @return SharpFormDropdownField
     */
    private function getDefaultDropdown($values = null)
    {
        return SharpFormDropdownField::make("field", $values ?: [
            "1" => "Elem 1",
            "2" => "Elem 2"
        ]);
    }
}