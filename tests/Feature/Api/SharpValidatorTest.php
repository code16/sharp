<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Form\Validator\SharpFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class SharpValidatorTest extends BaseApiTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    /** @test */
    public function we_can_validate_a_rtf_field()
    {
        $this->buildTheWorld();

        $this->app['config']->set(
            'sharp.entities.person.validator',
            ValidatorTestPersonSharpValidator::class
        );

        $this->json('post', '/sharp/api/form/person/1', [
            "name.text" => ""
        ])->assertStatus(422)
            ->assertJson([
                "errors" => [
                    "name" => ["The name field is required."] // Regular field name returned
                ]
            ]);
    }

    /** @test */
    public function the_sharp_form_request_base_class_handles_rtf_fields()
    {
        $this->buildTheWorld();

        $this->app['config']->set(
            'sharp.entities.person.validator',
            ValidatorTestPersonExtendingSharpFormRequestSharpValidator::class
        );

        $this->json('post', '/sharp/api/form/person/1', [
            "name.text" => ""
        ])->assertStatus(422)
            ->assertJson([
                "errors" => [
                    "name" => ["The name field is required."] // Regular field name returned
                ]
            ]);
    }
}

class ValidatorTestPersonSharpValidator extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return ['name.text' => 'required'];
    }
}

class ValidatorTestPersonExtendingSharpFormRequestSharpValidator extends SharpFormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        // No need for .text because we're extending SharpFormRequest
        return ['name' => 'required'];
    }
}