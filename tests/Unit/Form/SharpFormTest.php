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
use Code16\Sharp\Tests\SharpTestCase;

class SharpFormTest extends SharpTestCase
{

    /** @test */
    function we_get_formatted_data_in_creation_with_the_default_create_function()
    {
        $sharpForm = new class extends BaseSharpForm {
            function buildFormFields()
            {
                $this->addField(
                    SharpFormMarkdownField::make("md")
                )->addField(
                    SharpFormCheckField::make("check", "text")
                );
            }
        };

        $this->assertEquals([
                "md" => ["text" => null],
                "check" => false
            ], $sharpForm->newInstance());
    }

    /** @test */
    function we_get_formatted_data_in_creation_with_the_default_create_function_with_subclasses()
    {
        $sharpForm = new class extends BaseSharpForm {
            function buildFormFields()
            {
                $this->addField(
                    SharpFormTextField::make("name")
                )->addField(
                    SharpFormMarkdownField::make("subclass:company")
                );
            }
        };

        $this->assertEquals([
            "name" => "",
            "subclass:company" => ["text" => null],
        ], $sharpForm->newInstance());
    }

    /** @test */
    function if_the_field_formatter_needs_it_we_can_delay_its_execution_after_first_save()
    {
        $sharpForm = new class extends BaseSharpForm
        {
            public $instance;

            function buildFormFields()
            {
                $this->addField(
                    SharpFormTextField::make("normal")
                )->addField(
                    SharpFormTextField::make("delayed")
                        ->setFormatter(new class extends SharpFieldFormatter
                        {
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

        $this->assertEquals([
            "id" => 1,
            "normal" => "abc",
            "delayed" => "abc-1",
        ], $sharpForm->instance);
    }

    /** @test */
    function an_exception_is_raised_if_we_try_to_delay_but_the_update_does_not_return_the_instance_id()
    {
        $sharpForm = new class extends BaseSharpForm
        {
            function buildFormFields()
            {
                $this->addField(
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
}

class BaseSharpForm extends SharpForm
{
    function find($id): array
    {
    }
    function update($id, array $data)
    {
    }
    function delete($id)
    {
    }
    function buildFormFields()
    {
    }
    function buildFormLayout()
    {
    }
}