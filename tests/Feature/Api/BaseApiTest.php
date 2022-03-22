<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Tests\Fixtures\PersonSharpEntityList;
use Code16\Sharp\Tests\Fixtures\PersonSharpForm;
use Code16\Sharp\Tests\Fixtures\PersonSharpShow;
use Code16\Sharp\Tests\Fixtures\PersonSharpSingleShow;
use Code16\Sharp\Tests\Fixtures\PersonSharpValidator;
use Code16\Sharp\Tests\Fixtures\SharpDashboard;
use Code16\Sharp\Tests\Fixtures\User;
use Code16\Sharp\Tests\SharpTestCase;

abstract class BaseApiTest extends SharpTestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        if (! file_exists(public_path('vendor/sharp'))) {
            mkdir(public_path('vendor/sharp'), 0777, true);
        }
        touch(public_path('vendor/sharp/mix-manifest.json'));
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set(
            'app.key',
            'base64:'.base64_encode(random_bytes(
                $this->app['config']['app.cipher'] == 'AES-128-CBC' ? 16 : 32,
            )),
        );
    }

    protected function login()
    {
        $this->actingAs(new User());
    }

    protected function configurePersonValidator()
    {
        $this->app['config']->set(
            'sharp.entities.person.validator',
            PersonSharpValidator::class,
        );
    }

    protected function buildTheWorld($singleShow = false)
    {
        $this->app['config']->set(
            'sharp.entities.person.list',
            PersonSharpEntityList::class,
        );

        $this->app['config']->set(
            'sharp.entities.person.form',
            PersonSharpForm::class,
        );

        $this->app['config']->set(
            'sharp.dashboards.personal_dashboard.view',
            SharpDashboard::class,
        );

        if ($singleShow) {
            $this->app['config']->set(
                'sharp.entities.person.show',
                PersonSharpSingleShow::class,
            );
        } else {
            $this->app['config']->set(
                'sharp.entities.person.show',
                PersonSharpShow::class,
            );
        }
    }
}
