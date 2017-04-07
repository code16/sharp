<?php

namespace Code16\Sharp\Tests\Unit\Form\Layout;

use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Tests\SharpTestCase;

class FormLayoutTest extends SharpTestCase
{

    /** @test */
    function we_can_add_a_tab()
    {
        $form = new class extends FormLayoutTestForm {
            function buildFormLayout()
            {
                $this->addTab("label");
            }
        };

        $this->assertCount(1, $form->formLayout());
    }

    /** @test */
    function we_can_add_a_column()
    {
        $form = new class extends FormLayoutTestForm {
            function buildFormLayout()
            {
                $this->addColumn(2);
            }
        };

        $this->assertCount(1, $form->formLayout());
    }

    /** @test */
    function we_can_see_layout_as_array()
    {
        $form = new class extends FormLayoutTestForm {
            function buildFormLayout()
            {
                $this->addTab("label");
            }
        };

        $this->assertArraySubset(
            ["title" => "label", "columns" => []],
            $form->formLayout()[0]
        );

        $form2 = new class extends FormLayoutTestForm {
            function buildFormLayout()
            {
                $this->addColumn(2);
            }
        };

        $this->assertArraySubset([
            "columns" => [
                ["size" => 2]
            ]],
            $form2->formLayout()[0]
        );
    }
}

abstract class FormLayoutTestForm extends SharpForm
{
    function get($id): array { return []; }
    function update($id, array $data): bool { return false; }
    function store(array $data): bool { return false; }
    function delete($id): bool { return false; }
    function buildFormFields() {}
}