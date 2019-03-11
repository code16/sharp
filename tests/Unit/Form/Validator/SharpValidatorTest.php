<?php

namespace Code16\Sharp\Tests\Unit\Form\Validator;

use Code16\Sharp\Form\Validator\SharpValidator;
use Code16\Sharp\Tests\SharpTestCase;
use Illuminate\Validation\ValidationException;

class SharpValidatorTest extends SharpTestCase
{

    protected function setUp(): void
    {
        parent::setUp();

        // Bind Sharp's Validator
        app()->validator->resolver(function($translator, $data, $rules, $messages) {
            return new SharpValidator($translator, $data, $rules, $messages);
        });
    }

    /** @test */
    function the_messages_bag_converts_text_suffixed_data()
    {
        $validator = $this->app->validator->make([
            "name" => "John Wayne",
        ], [
            "name" => "required",
            "bio.text" => "required"
        ]);

        $validator->passes();

        $this->assertEquals(["bio" => ["The bio field is required."]], $validator->messages()->toArray());
    }

    /** @test */
    function the_messages_bag_does_not_converts_non_text_suffixed_data()
    {
        $validator = $this->app->validator->make([
        ], [
            "name" => "required",
        ]);

        $validator->passes();

        $this->assertEquals(["name" => ["The name field is required."]], $validator->messages()->toArray());
    }

    /** @test */
    function compatible_with_laravel_validation_exception()
    {
        $exception = ValidationException::withMessages([
            "name" => ["Test"]
        ]);

        $this->assertEquals(["name" => ["Test"]], $exception->errors());
    }
}