<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields;

use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Tests\SharpTestCase;

class SharpFormFieldWithDataLocalizationTest extends SharpTestCase
{

    /** @test */
    function we_can_define_the_localized_attribute_for_text_field()
    {
        $formField = SharpFormTextField::make("name")
            ->setLocalized(false);

        $this->assertArrayNotHasKey(
            "localized", $formField->toArray()
        );

        $formField->setLocalized();

        $this->assertArraySubset(
            ["localized" => true], $formField->toArray()
        );
    }
}