<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Tests\Fixtures\PersonSharpEntityList;
use Code16\Sharp\Tests\Fixtures\PersonSharpForm;
use Code16\Sharp\Tests\Fixtures\PersonSharpValidator;
use Code16\Sharp\Tests\Fixtures\User;
use Code16\Sharp\Tests\SharpTestCase;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Orchestra\Testbench\Exceptions\Handler;

abstract class BaseApiTest extends SharpTestCase
{
    protected function login()
    {
        $this->actingAs(new User);
    }

    protected function disableExceptionHandling()
    {
        $this->app->instance(ExceptionHandler::class, new class extends Handler {
            public function __construct() {}
            public function report(\Exception $e) {}
            public function render($request, \Exception $e) {
                throw $e;
            }
        });
    }

    protected function configurePersonValidator()
    {
        $this->app['config']->set(
            'sharp.entities.person.validator',
            PersonSharpValidator::class
        );
    }

    protected function buildTheWorld()
    {
        $this->app['config']->set(
            'sharp.entities.person.list',
            PersonSharpEntityList::class
        );

        $this->app['config']->set(
            'sharp.entities.person.form',
            PersonSharpForm::class
        );

        $this->app['config']->set(
            'app.key', 'base64:'.base64_encode(random_bytes(
                $this->app['config']['app.cipher'] == 'AES-128-CBC' ? 16 : 32
            ))
        );
    }
}