<?php

namespace Code16\Sharp\Tests\Unit\Form;

use Code16\Sharp\Exceptions\Form\SharpFormFieldFormattingMustBeDelayedException;
use Code16\Sharp\Exceptions\Form\SharpFormUpdateException;
use Code16\Sharp\Form\Fields\Formatters\SharpFieldFormatter;
use Code16\Sharp\Form\Fields\SharpFormCheckField;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Form\SharpSingleForm;
use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class SharpFormTest extends SharpTestCase
{
    /** @test */
    public function we_get_formatted_data_in_creation_with_the_default_create_function()
    {
        $sharpForm = new class extends BaseSharpForm
        {
            public function buildFormFields(FieldsContainer $formFields): void
            {
                $formFields
                    ->addField(SharpFormEditorField::make('md'))
                    ->addField(SharpFormCheckField::make('check', 'text'));
            }
        };

        $this->assertEquals(
            [
                'md' => ['text' => null],
                'check' => false,
            ],
            $sharpForm->newInstance(),
        );
    }

    /** @test */
    public function we_get_formatted_data_in_creation_with_the_default_create_function_with_subclasses()
    {
        $sharpForm = new class extends BaseSharpForm
        {
            public function buildFormFields(FieldsContainer $formFields): void
            {
                $formFields
                    ->addField(SharpFormTextField::make('name'))
                    ->addField(SharpFormEditorField::make('subclass:company'));
            }
        };

        $this->assertEquals(
            [
                'name' => '',
                'subclass:company' => ['text' => null],
            ],
            $sharpForm->newInstance(),
        );
    }

    /** @test */
    public function if_the_field_formatter_needs_it_we_can_delay_its_execution_after_first_save()
    {
        $sharpForm = new class extends BaseSharpForm
        {
            public $instance;

            public function buildFormFields(FieldsContainer $formFields): void
            {
                $formFields
                    ->addField(
                        SharpFormTextField::make('normal'),
                    )
                    ->addField(
                        SharpFormTextField::make('delayed')
                            ->setFormatter(new class extends SharpFieldFormatter
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

        $this->assertEquals(
            [
                'id' => 1,
                'normal' => 'abc',
                'delayed' => 'abc-1',
            ],
            $sharpForm->instance,
        );
    }

    /** @test */
    public function an_exception_is_raised_if_we_try_to_delay_but_the_update_does_not_return_the_instance_id()
    {
        $sharpForm = new class extends BaseSharpForm
        {
            public function buildFormFields(FieldsContainer $formFields): void
            {
                $formFields->addField(
                    SharpFormTextField::make('delayed')
                        ->setFormatter(new class extends SharpFieldFormatter
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
        $sharpForm = new class extends BaseSharpSingleForm
        {
        };

        $sharpForm->buildFormConfig();

        $this->assertEquals(
            [
                'isSingle' => true,
                'hasShowPage' => false,
                'confirmTextOnDeletion' => null,
            ],
            $sharpForm->formConfig(),
        );
    }

    /** @test */
    public function we_can_declare_setDisplayShowPageAfterCreation_in_config()
    {
        $sharpForm = new class extends BaseSharpForm
        {
            public function buildFormConfig(): void
            {
                $this->configureDisplayShowPageAfterCreation(true);
            }
        };

        $sharpForm->buildFormConfig();

        $this->assertEquals(
            [
                'hasShowPage' => true,
                'confirmTextOnDeletion' => null,
            ],
            $sharpForm->formConfig(),
        );
    }

    /** @test */
    public function we_can_declare_configureConfirmTextOnDeletion_with_custom_text_in_config()
    {
        $sharpForm = new class extends BaseSharpForm
        {
            public function buildFormConfig(): void
            {
                $this->configureConfirmTextOnDeletion('Vous êtes sûr ?');
            }
        };

        $sharpForm->buildFormConfig();

        $this->assertEquals(
            [
                'hasShowPage' => false,
                'confirmTextOnDeletion' => 'Vous êtes sûr ?',
            ],
            $sharpForm->formConfig(),
        );
    }

    /** @test */
    public function we_can_declare_configureConfirmTextOnDeletion_with_default_text_in_config()
    {
        $sharpForm = new class extends BaseSharpForm
        {
            public function buildFormConfig(): void
            {
                $this->configureConfirmTextOnDeletion();
            }
        };

        $sharpForm->buildFormConfig();

        $this->assertEquals(
            [
                'hasShowPage' => false,
                'confirmTextOnDeletion' => 'Are you sure you want to delete this item ?',
            ],
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

    public function buildFormFields(FieldsContainer $formFields): void
    {
    }

    public function buildFormLayout(FormLayout $formLayout): void
    {
    }
}

class BaseSharpSingleForm extends SharpSingleForm
{
    public function buildFormFields(FieldsContainer $formFields): void
    {
    }

    public function buildFormLayout(FormLayout $formLayout): void
    {
    }

    protected function findSingle()
    {
    }

    protected function updateSingle(array $data)
    {
    }
}
