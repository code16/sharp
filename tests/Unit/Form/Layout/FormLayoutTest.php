<?php

namespace Code16\Sharp\Tests\Unit\Form\Layout;

use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Tests\SharpTestCase;

class FormLayoutTest extends SharpTestCase
{
    /** @test */
    public function we_can_add_a_tab()
    {
        $form = new class() extends FormLayoutTestForm
        {
            public function buildFormLayout(): void
            {
                $this->addTab('label');
            }
        };

        $this->assertCount(1, $form->formLayout()['tabs']);
    }

    /** @test */
    public function we_can_add_a_column()
    {
        $form = new class() extends FormLayoutTestForm
        {
            public function buildFormLayout(): void
            {
                $this->addColumn(2);
            }
        };

        $this->assertCount(1, $form->formLayout()['tabs'][0]['columns']);
    }

    /** @test */
    public function we_can_see_layout_as_array()
    {
        $form = new class() extends FormLayoutTestForm
        {
            public function buildFormLayout(): void
            {
                $this->addTab('label');
            }
        };

        $this->assertArraySubset(
            ['title' => 'label', 'columns' => []],
            $form->formLayout()['tabs'][0],
        );

        $form2 = new class() extends FormLayoutTestForm
        {
            public function buildFormLayout(): void
            {
                $this->addColumn(2);
            }
        };

        $this->assertArraySubset(
            [
                'columns' => [
                    ['size' => 2],
                ], ],
            $form2->formLayout()['tabs'][0],
        );
    }

    /** @test */
    public function we_can_set_tabbed_to_false()
    {
        $form = new class() extends FormLayoutTestForm
        {
            public function buildFormLayout(): void
            {
                $this->addTab('label')->setTabbed(false);
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

    public function buildFormFields(): void
    {
    }
}
