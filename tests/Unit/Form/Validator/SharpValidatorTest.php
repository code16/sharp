<?php

namespace Code16\Sharp\Tests\Unit\Form\Validator;

use Code16\Sharp\Form\Validator\SharpValidator;
use Code16\Sharp\Tests\SharpTestCase;

class SharpValidatorTest extends SharpTestCase
{

    /** @test */
    function the_sharp_validator_is_binded()
    {
        $this->assertInstanceOf(SharpValidator::class, $this->app->validator->make([], []));
    }

    /** @test */
    function the_messages_bag_converts_text_suffixed_data()
    {
        $validator = $this->app->validator->make([
            "name" => "John Wayne",
//            "bio" => ["text" => "lorem ipsum"]
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
}