<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Tests\Fixtures\PersonSharpEntityList;
use Code16\Sharp\Tests\Fixtures\PersonSharpForm;
use Code16\Sharp\Tests\Fixtures\PersonSharpValidator;
use Code16\Sharp\Tests\Fixtures\SharpDashboard;
use Code16\Sharp\Tests\Fixtures\User;
use Code16\Sharp\Tests\SharpTestCase;

abstract class BaseApiTest extends SharpTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set(
            'app.key', 'base64:'.base64_encode(random_bytes(
                $this->app['config']['app.cipher'] == 'AES-128-CBC' ? 16 : 32
            ))
        );
    }

    protected function login()
    {
        $this->actingAs(new User);
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
            'sharp.dashboards.personal_dashboard.view',
            SharpDashboard::class
        );
    }
}