<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Form\BuildsSharpFormFields;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Form\SharpFormData;
use Code16\Sharp\Form\SharpFormException;
use Code16\Sharp\Tests\SharpTestCase;
use Illuminate\Foundation\Http\FormRequest;

abstract class BaseApiTest extends SharpTestCase
{

    protected function buildTheWorld($validator = false)
    {
        $this->app['config']->set(
            'sharp.entities.person.data',
            PersonSharpForm::class
        );

        $this->app['config']->set(
            'sharp.entities.person.form',
            PersonSharpForm::class
        );

        if ($validator) {
            $this->app['config']->set(
                'sharp.entities.person.validator',
                PersonSharpValidator::class
            );
        }
    }
}

class PersonSharpForm implements SharpFormData, SharpForm
{
    use BuildsSharpFormFields;

    function buildForm()
    {
        $this->addField(SharpFormTextField::make("name"));
    }

    function get($id): array
    {
        return ["name" => "John Wayne"];
    }

    function update($id, array $data): bool
    {
        if(!intval($id)) {
            throw new SharpFormException("$id is not a valid id");
        }

        return true;
    }

    function store(array $data): bool
    {
        return true;
    }

    function delete($id): bool
    {
        return true;
    }
}

class PersonSharpValidator extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return ['name' => 'required'];
    }
}