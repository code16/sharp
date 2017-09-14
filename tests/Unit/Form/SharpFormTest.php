<?php

namespace Code16\Sharp\Tests\Unit\Form;

use Code16\Sharp\Form\Fields\SharpFormCheckField;
use Code16\Sharp\Form\Fields\SharpFormMarkdownField;
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