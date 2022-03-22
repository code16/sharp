<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields;

use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Tests\SharpTestCase;

class SharpFormFieldsTest extends SharpTestCase
{
    /** @test */
    public function we_can_add_a_field()
    {
        $form = new class() extends FormFieldsTestForm
        {
            public function buildFormFields(): void
            {
                $this->addField(SharpFormTextField::make('name'));
                $this->addField(SharpFormTextField::make('first_name'));
            }
        };

        $this->assertCount(2, $form->fields());
    }

    /** @test */
    public function we_can_see_fields_as_array()
    {
        $form = new class() extends FormFieldsTestForm
        {
            public function buildFormFields(): void
            {
                $this->addField(SharpFormTextField::make('name'));
                $this->addField(SharpFormTextField::make('first_name'));
            }
        };

        $this->assertArraySubset(
            ['type' => 'text'],
            $form->fields()['name'],
        );
        $this->assertArraySubset(
            ['type' => 'text'],
            $form->fields()['first_name'],
        );
    }
}

abstract class FormFieldsTestForm extends SharpForm
{
    public function find($id): array
    {
        return [];
    }

    public function update($id, array $data)
    {
        return false;
    }

    public function delete($id): void
    {
    }

    public function buildFormLayout(): void
    {
    }
}
