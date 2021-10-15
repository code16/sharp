<?php

namespace Code16\Sharp\Tests\Unit\Form;

use Code16\Sharp\Exceptions\Form\SharpFormFieldFormattingMustBeDelayedException;
use Code16\Sharp\Exceptions\Form\SharpFormUpdateException;
use Code16\Sharp\Form\Fields\Formatters\SharpFieldFormatter;
use Code16\Sharp\Form\Fields\SharpFormCheckField;
use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\SharpFormMarkdownField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Form\SharpSingleForm;
use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class SharpFormTest extends SharpTestCase
{

    /** @test */
    function we_get_formatted_data_in_creation_with_the_default_create_function()
    {
        $sharpForm = new class extends BaseSharpForm {
            function buildFormFields(FieldsContainer $formFields): void
            {
                $formFields
                    ->addField(SharpFormMarkdownField::make("md"))
                    ->addField(SharpFormCheckField::make("check", "text"));
            }
        };

        $this->assertEquals(
            [
                "md" => ["text" => null],
                "check" => false
            ], 
            $sharpForm->newInstance()
        );
    }

    /** @test */
    function we_get_formatted_data_in_creation_with_the_default_create_function_with_subclasses()
    {
        $sharpForm = new class extends BaseSharpForm {
            function buildFormFields(FieldsContainer $formFields): void
            {
                $formFields
                    ->addField(SharpFormTextField::make("name"))
                    ->addField(SharpFormMarkdownField::make("subclass:company"));
            }
        };

        $this->assertEquals(
            [
                "name" => "",
                "subclass:company" => ["text" => null],
            ],
            $sharpForm->newInstance()
        );
    }

    /** @test */
    function if_the_field_formatter_needs_it_we_can_delay_its_execution_after_first_save()
    {
        $sharpForm = new class extends BaseSharpForm
        {
            public $instance;

            function buildFormFields(FieldsContainer $formFields): void
            {
                $formFields
                    ->addField(
                        SharpFormTextField::make("normal")
                    )
                    ->addField(
                        SharpFormTextField::make("delayed")
                            ->setFormatter(new class extends SharpFieldFormatter {
                                function toFront(SharpFormField $field, $value)
                                {
                                }
                                function fromFront(SharpFormField $field, string $attribute, $value)
                                {
                                    if (!$this->instanceId) {
                                        throw new SharpFormFieldFormattingMustBeDelayedException();
                                    }
    
                                    return $value . "-" . $this->instanceId;
                                }
                            })
                    );
            }

            function update($id, array $data)
            {
                if (!$id) {
                    $this->instance = ["id" => 1] + $data;

                } else {
                    $this->instance += $data;
                }

                return 1;
            }
        };

        $sharpForm->storeInstance([
            "normal" => "abc",
            "delayed" => "abc",
        ]);

        $this->assertEquals(
            [
                "id" => 1,
                "normal" => "abc",
                "delayed" => "abc-1",
            ], 
            $sharpForm->instance
        );
    }

    /** @test */
    function an_exception_is_raised_if_we_try_to_delay_but_the_update_does_not_return_the_instance_id()
    {
        $sharpForm = new class extends BaseSharpForm
        {
            function buildFormFields(FieldsContainer $formFields): void
            {
                $formFields->addField(
                    SharpFormTextField::make("delayed")
                        ->setFormatter(new class extends SharpFieldFormatter
                        {
                            function toFront(SharpFormField $field, $value)
                            {
                            }

                            function fromFront(SharpFormField $field, string $attribute, $value)
                            {
                                throw new SharpFormFieldFormattingMustBeDelayedException();
                            }
                        })
                );
            }
        };

        $this->expectException(SharpFormUpdateException::class);
        $sharpForm->storeInstance([
            "delayed" => "abc",
        ]);
    }

    /** @test */
    function single_forms_are_declared_in_config()
    {
        $sharpForm = new class extends BaseSharpSingleForm
        {
        };
        
        $sharpForm->buildFormConfig();

        $this->assertEquals(
            [
                "isSingle" => true, 
                "hasShowPage" => false
            ], 
            $sharpForm->formConfig()
        );
    }

    /** @test */
    function we_can_declare_setDisplayShowPageAfterCreation_in_config()
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
            ["hasShowPage" => true],
            $sharpForm->formConfig()
        );
    }
}

class BaseSharpForm extends SharpForm
{
    function find($id): array
    {
    }
    function update($id, array $data)
    {
    }
    function delete($id): void
    {
    }
    function buildFormFields(FieldsContainer $formFields): void
    {
    }
    function buildFormLayout(FormLayout $formLayout): void
    {
    }
}

class BaseSharpSingleForm extends SharpSingleForm
{
    function buildFormFields(FieldsContainer $formFields): void
    {
    }
    function buildFormLayout(FormLayout $formLayout): void
    {
    }
    protected function findSingle()
    {
    }
    protected function updateSingle(array $data)
    {
    }
}