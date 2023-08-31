<?php

namespace Code16\Sharp\Tests\Unit\Form\Layout;

use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class FormLayoutTest extends SharpTestCase
{
    /** @test */
    public function we_can_add_a_tab()
    {
        $form = new class extends FormLayoutTestForm
        {
            public function buildFormLayout(FormLayout $formLayout): void
            {
                $formLayout->addTab('label');
            }
        };

        $this->assertCount(1, $form->formLayout()['tabs']);
    }

    /** @test */
    public function we_can_add_a_column()
    {
        $form = new class extends FormLayoutTestForm
        {
            public function buildFormLayout(FormLayout $formLayout): void
            {
                $formLayout->addColumn(2);
            }
        };

        $this->assertCount(1, $form->formLayout()['tabs'][0]['columns']);
    }

    /** @test */
    public function we_can_see_layout_as_array()
    {
        $form = new class extends FormLayoutTestForm
        {
            public function buildFormLayout(FormLayout $formLayout): void
            {
                $formLayout->addTab('label');
            }
        };

        $this->assertArraySubset(
            ['title' => 'label', 'columns' => []],
            $form->formLayout()['tabs'][0],
        );

        $form2 = new class extends FormLayoutTestForm
        {
            public function buildFormLayout(FormLayout $formLayout): void
            {
                $formLayout->addColumn(2);
            }
        };

        $this->assertArraySubset(
            [
                'columns' => [
                    ['size' => 2],
                ],
            ],
            $form2->formLayout()['tabs'][0],
        );
    }

    /** @test */
    public function we_can_set_tabbed_to_false()
    {
        $form = new class extends FormLayoutTestForm
        {
            public function buildFormLayout(FormLayout $formLayout): void
            {
                $formLayout->addTab('label')->setTabbed(false);
            }
        };

        $this->assertFalse($form->formLayout()['tabbed']);
    }
}

abstract class FormLayoutTestForm extends SharpForm
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

    public function buildFormFields(FieldsContainer $formFields): void
    {
    }
}
