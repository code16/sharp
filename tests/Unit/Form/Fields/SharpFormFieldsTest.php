<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields;

use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class SharpFormFieldsTest extends SharpTestCase
{

    /** @test */
    function we_can_add_a_field()
    {
        $form = new class extends FormFieldsTestForm {
            function buildFormFields(FieldsContainer $formFields): void
            {
                $formFields->addField(SharpFormTextField::make("name"))
                    ->addField(SharpFormTextField::make("first_name"));
            }
        };

        $this->assertCount(2, $form->fields());
    }

    /** @test */
    function we_can_see_fields_as_array()
    {
        $form = new class extends FormFieldsTestForm {
            function buildFormFields(FieldsContainer $formFields): void
            {
                $formFields->addField(SharpFormTextField::make("name"))
                    ->addField(SharpFormTextField::make("first_name"));
            }
        };

        $this->assertArraySubset(
            ["type" => "text"],
            $form->fields()["name"]
        );
        $this->assertArraySubset(
            ["type" => "text"],
            $form->fields()["first_name"]
        );
    }
}

abstract class FormFieldsTestForm extends SharpForm
{
    function find($id): array { return []; }
    function update($id, array $data) { return false; }
    function delete($id): void {}
    function buildFormLayout(FormLayout $formLayout): void {}
}