<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Http\SharpContext;
use Illuminate\Foundation\Http\FormRequest;

class ContextTest extends BaseApiTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    /** @test */
    public function context_is_set_on_an_edit_case()
    {
        $this->buildTheWorld();

        $context = app(SharpContext::class);

        $this->json('get', '/sharp/api/form/person/50');

        $this->assertTrue($context->isUpdate());
        $this->assertEquals("50", $context->instanceId());
    }

    /** @test */
    public function context_is_set_on_a_update_case()
    {
        $this->buildTheWorld();
        $this->configurePersonValidator();

        $context = app(SharpContext::class);

        $this->json('post', '/sharp/api/form/person/50', [
            "name" => "Jane Fonda"
        ]);

        $this->assertTrue($context->isUpdate());
        $this->assertEquals("50", $context->instanceId());
    }

    /** @test */
    public function context_is_set_on_a_create_case()
    {
        $this->buildTheWorld();

        $context = app(SharpContext::class);

        $this->json('get', '/sharp/api/form/person');

        $this->assertTrue($context->isCreation());
    }

    /** @test */
    public function context_is_set_on_a_store_case()
    {
        $this->buildTheWorld();
        $this->configurePersonValidator();

        $context = app(SharpContext::class);

        $this->json('post', '/sharp/api/form/person', [
            "name" => "Jane Fonda"
        ]);

        $this->assertTrue($context->isCreation());
    }

    /** @test */
    public function context_is_set_in_the_validation()
    {
        $this->buildTheWorld();

        $this->app['config']->set(
            'sharp.entities.person.validator',
            PersonSharpValidatorWithContext::class
        );

        $this->json('post', '/sharp/api/form/person', [
            "name" => "Jane Fonda"
        ])->assertStatus(200);

        $this->json('post', '/sharp/api/form/person/1', [
            "name" => "Jane Fonda"
        ])->assertStatus(422);

        $this->json('post', '/sharp/api/form/person/1', [
            "name" => "Jane Fonda",
            "age" => 42
        ])->assertStatus(200);
    }
}

class PersonSharpValidatorWithContext extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(SharpContext $context)
    {
        if($context->isUpdate()) {
            return [
                'name' => 'required',
                'age' => 'required'
            ];
        }

        return [
            'name' => 'required',
        ];
    }
}