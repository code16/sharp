<?php

namespace Code16\Sharp\Tests\Unit\Form;

use Code16\Sharp\Exceptions\Form\SharpFormFieldFormattingMustBeDelayedException;
use Code16\Sharp\Exceptions\Form\SharpFormUpdateException;
use Code16\Sharp\Form\Fields\Formatters\SharpFieldFormatter;
use Code16\Sharp\Form\Fields\SharpFormCheckField;
use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\SharpFormMarkdownField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Form\SharpSingleForm;
use Code16\Sharp\Tests\SharpTestCase;

class SharpFormTest extends SharpTestCase
{
    /** @test */
    public function we_get_formatted_data_in_creation_with_the_default_create_function()
    {
        $sharpForm = new class() extends BaseSharpForm
        {
            public function buildFormFields(): void
            {
                $this->addField(
                    SharpFormMarkdownField::make('md'),
                )->addField(
                    SharpFormCheckField::make('check', 'text'),
                );
            }
        };

        $this->assertEquals([
            'md' => ['text' => null],
            'check' => false,
        ], $sharpForm->newInstance());
    }

    /** @test */
    public function we_get_formatted_data_in_creation_with_the_default_create_function_with_subclasses()
    {
        $sharpForm = new class() extends BaseSharpForm
        {
            public function buildFormFields(): void
            {
                $this->addField(
                    SharpFormTextField::make('name'),
                )->addField(
                    SharpFormMarkdownField::make('subclass:company'),
                );
            }
        };

        $this->assertEquals([
            'name' => '',
            'subclass:company' => ['text' => null],
        ], $sharpForm->newInstance());
    }

    /** @test */
    public function if_the_field_formatter_needs_it_we_can_delay_its_execution_after_first_save()
    {
        $sharpForm = new class() extends BaseSharpForm
        {
            public $instance;

            public function buildFormFields(): void
            {
                $this->addField(
                    SharpFormTextField::make('normal'),
                )->addField(
                    SharpFormTextField::make('delayed')
                        ->setFormatter(new class() extends SharpFieldFormatter
                        {
                            public function toFront(SharpFormField $field, $value)
                            {
                            }

                            public function fromFront(SharpFormField $field, string $attribute, $value)
                            {
                                if (! $this->instanceId) {
                                    throw new SharpFormFieldFormattingMustBeDelayedException();
                                }

                                return $value.'-'.$this->instanceId;
                            }
                        }, ),
                );
            }

            public function update($id, array $data)
            {
                if (! $id) {
                    $this->instance = ['id' => 1] + $data;
                } else {
                    $this->instance += $data;
                }

                return 1;
            }
        };

        $sharpForm->storeInstance([
            'normal' => 'abc',
            'delayed' => 'abc',
        ]);

        $this->assertEquals([
            'id' => 1,
            'normal' => 'abc',
            'delayed' => 'abc-1',
        ], $sharpForm->instance);
    }

    /** @test */
    public function an_exception_is_raised_if_we_try_to_delay_but_the_update_does_not_return_the_instance_id()
    {
        $sharpForm = new class() extends BaseSharpForm
        {
            public function buildFormFields(): void
            {
                $this->addField(
                    SharpFormTextField::make('delayed')
                        ->setFormatter(new class() extends SharpFieldFormatter
                        {
                            public function toFront(SharpFormField $field, $value)
                            {
                            }

                            public function fromFront(SharpFormField $field, string $attribute, $value)
                            {
                                throw new SharpFormFieldFormattingMustBeDelayedException();
                            }
                        }, ),
                );
            }
        };

        $this->expectException(SharpFormUpdateException::class);
        $sharpForm->storeInstance([
            'delayed' => 'abc',
        ]);
    }

    /** @test */
    public function single_forms_are_declared_in_config()
    {
        $sharpForm = new class() extends BaseSharpSingleForm
        {
        };

        $sharpForm->buildFormConfig();

        $this->assertEquals(
            [
                'isSingle' => true,
                'hasShowPage' => false,
            ],
            $sharpForm->formConfig(),
        );
    }

    /** @test */
    public function we_can_declare_setDisplayShowPageAfterCreation_in_config()
    {
        $sharpForm = new class() extends BaseSharpForm
        {
            public function buildFormConfig(): void
            {
                $this->setDisplayShowPageAfterCreation(true);
            }
        };

        $sharpForm->buildFormConfig();

        $this->assertEquals(
            ['hasShowPage' => true],
            $sharpForm->formConfig(),
        );
    }
}

class BaseSharpForm extends SharpForm
{
    public function find($id): array
    {
    }

    public function update($id, array $data)
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

class BaseSharpSingleForm extends SharpSingleForm
{
    public function buildFormFields(): void
    {
    }

    public function buildFormLayout(): void
    {
    }

    protected function findSingle()
    {
    }

    protected function updateSingle(array $data)
    {
    }
}
