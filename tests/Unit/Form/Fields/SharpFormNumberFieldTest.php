<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields;

use Code16\Sharp\Form\Fields\SharpFormNumberField;
use Code16\Sharp\Tests\SharpTestCase;

class SharpFormNumberFieldTest extends SharpTestCase
{
    /** @test */
    function only_default_values_are_set()
    {
        $formField = SharpFormNumberField::make("text");

        $this->assertEquals([
                "key" => "text", "type" => "number",
                "step" => 1, "showControls" => false
            ], $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_min_and_max()
    {
        $formField = SharpFormNumberField::make("text")
            ->setMin(5)
            ->setMax(15);

        $this->assertArrayContainsSubset(
            ["min" => 5, "max" => 15],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_a_step()
    {
        $formField = SharpFormNumberField::make("text")
            ->setStep(5);

        $this->assertArrayContainsSubset(
            ["step" => 5],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_showControls()
    {
        $formField = SharpFormNumberField::make("text")
            ->setShowControls();

        $this->assertArrayContainsSubset(
            ["showControls" => true],
            $formField->toArray()
        );
    }
}