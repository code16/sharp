<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields;

use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Tests\SharpTestCase;

class SharpFormTest extends SharpTestCase
{
    /** @test */
    function we_can_get_fields()
    {
        $form = new class extends SharpFormTestForm {
            function buildFormFields(): void
            {
                $this->addField(SharpFormTextField::make("name"));
            }
        };

        $this->assertEquals(["name" => [
            "key" => "name",
            "type" => "text",
            "maxLength" => 0,
            "inputType" => "text"
        ]], $form->fields());
    }

    /** @test */
    function we_can_get_layout()
    {
        $form = new class extends SharpFormTestForm {
            function buildFormFields(): void
            {
                $this->addField(SharpFormTextField::make("name"))
                    ->addField(SharpFormTextField::make("age"));
            }
            function buildFormLayout(): void
            {
                $this->addColumn(6, function($column) {
                    $column->withSingleField("name");
                })->addColumn(6, function($column) {
                    $column->withSingleField("age");
                });
            }
        };

        $this->assertEquals([
            "tabbed" => true,
            "tabs" => [[
                "title" => "one",
                "columns" => [[
                    "size" => 6,
                    "fields" => [[
                        [
                            "key" => "name",
                            "size" => 12,
                            "sizeXS" => 12
                        ]
                    ]]
                ], [
                    "size" => 6,
                    "fields" => [[
                        [
                            "key" => "age",
                            "size" => 12,
                            "sizeXS" => 12
                        ]
                    ]]
                ]]
            ]]
        ], $form->formLayout());
    }

    /** @test */
    function we_can_get_instance()
    {
        $form = new class extends SharpFormTestForm {
            function find($id): array
            {
                return [
                    "name" => "John Wayne",
                    "age" => 22,
                    "job" => "actor"
                ];
            }
            function buildFormFields(): void
            {
                $this->addField(SharpFormTextField::make("name"))
                    ->addField(SharpFormTextField::make("age"));
            }
        };

        $this->assertEquals([
            "name" => "John Wayne",
            "age" => 22
        ], $form->instance(1));
    }
}

abstract class SharpFormTestForm extends SharpForm
{
    function find($id): array {}
    function update($id, array $data): bool {}
    function delete($id): void {}
    function buildFormFields(): void {}
    function buildFormLayout(): void {}
}