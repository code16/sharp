<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields;

use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Tests\SharpTestCase;

class SharpFormTest extends SharpTestCase
{
    /** @test */
    public function we_can_get_fields()
    {
        $form = new class() extends SharpFormTestForm {
            public function buildFormFields(): void
            {
                $this->addField(SharpFormTextField::make('name'));
            }
        };

        $this->assertEquals(['name' => [
            'key'       => 'name',
            'type'      => 'text',
            'inputType' => 'text',
        ]], $form->fields());
    }

    /** @test */
    public function we_can_get_layout()
    {
        $form = new class() extends SharpFormTestForm {
            public function buildFormFields(): void
            {
                $this->addField(SharpFormTextField::make('name'))
                    ->addField(SharpFormTextField::make('age'));
            }

            public function buildFormLayout(): void
            {
                $this->addColumn(6, function ($column) {
                    $column->withSingleField('name');
                })->addColumn(6, function ($column) {
                    $column->withSingleField('age');
                });
            }
        };

        $this->assertEquals([
            'tabbed' => true,
            'tabs'   => [[
                'title'   => 'one',
                'columns' => [[
                    'size'   => 6,
                    'fields' => [[
                        [
                            'key'    => 'name',
                            'size'   => 12,
                            'sizeXS' => 12,
                        ],
                    ]],
                ], [
                    'size'   => 6,
                    'fields' => [[
                        [
                            'key'    => 'age',
                            'size'   => 12,
                            'sizeXS' => 12,
                        ],
                    ]],
                ]],
            ]],
        ], $form->formLayout());
    }

    /** @test */
    public function we_can_get_instance()
    {
        $form = new class() extends SharpFormTestForm {
            public function find($id): array
            {
                return [
                    'name' => 'John Wayne',
                    'age'  => 22,
                    'job'  => 'actor',
                ];
            }

            public function buildFormFields(): void
            {
                $this->addField(SharpFormTextField::make('name'))
                    ->addField(SharpFormTextField::make('age'));
            }
        };

        $this->assertEquals([
            'name' => 'John Wayne',
            'age'  => 22,
        ], $form->instance(1));
    }
}

abstract class SharpFormTestForm extends SharpForm
{
    public function find($id): array
    {
    }

    public function update($id, array $data): bool
    {
    }

    public function delete($id): void
    {
    }

    public function buildFormFields(): void
    {
    }

    public function buildFormLayout(): void
    {
    }
}
