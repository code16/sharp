<?php

namespace Code16\Sharp\Tests\Unit\Form;

use Code16\Sharp\Form\BuildsSharpFormFields;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Tests\SharpTestCase;

class BuildsSharpFormFieldsTest extends SharpTestCase
{

    /** @test */
    function we_can_add_a_field()
    {
        $form = $this->getObjectForTrait(BuildsSharpFormFields::class);
        $form->addField(SharpFormTextField::make("name"));
        $form->addField(SharpFormTextField::make("first_name"));

        $this->assertCount(2, $form->buildForm());
    }

    /** @test */
    function we_can_see_fields_as_array()
    {
        $form = $this->getObjectForTrait(BuildsSharpFormFields::class);
        $form->addField(SharpFormTextField::make("name"));
        $form->addField(SharpFormTextField::make("first_name"));

        $this->assertArraySubset(
            ["key" => "name", "type" => "text"],
            $form->buildForm()[0]
        );
        $this->assertArraySubset(
            ["key" => "first_name", "type" => "text"],
            $form->buildForm()[1]
        );
    }
}