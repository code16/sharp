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

        $this->assertCount(1, $form->formLayout()["tabs"]);
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

        $this->assertCount(1, $form->formLayout()["tabs"][0]["columns"]);
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

        $this->assertArrayContainsSubset(
            ["title" => "label", "columns" => []],
            $form->formLayout()["tabs"][0]
        );

        $form2 = new class extends FormLayoutTestForm {
            function buildFormLayout()
            {
                $this->addColumn(2);
            }
        };

        $this->assertArrayContainsSubset([
            "columns" => [
                ["size" => 2]
            ]],
            $form2->formLayout()["tabs"][0]
        );
    }

    /** @test */
    function we_can_set_tabbed_to_false()
    {
        $form = new class extends FormLayoutTestForm {
            function buildFormLayout()
            {
                $this->addTab("label")->setTabbed(false);
            }
        };

        $this->assertFalse($form->formLayout()["tabbed"]);
    }
}

abstract class FormLayoutTestForm extends SharpForm
{
    function find($id): array { return []; }
    function update($id, array $data): bool { return false; }
    function delete($id): bool { return false; }
    function buildFormFields() {}
}