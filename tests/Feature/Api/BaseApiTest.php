<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Tests\Fixtures\PersonSharpForm;
use Code16\Sharp\Tests\Fixtures\PersonSharpValidator;
use Code16\Sharp\Tests\SharpTestCase;

abstract class BaseApiTest extends SharpTestCase
{

    protected function buildTheWorld($validator = false)
    {
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