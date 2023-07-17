<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Tests\Fixtures\PersonalDashboardEntity;
use Code16\Sharp\Tests\Fixtures\PersonEntity;
use Code16\Sharp\Tests\Fixtures\SinglePersonEntity;
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
            'app.key', 'base64:'.base64_encode(random_bytes(
                $this->app['config']['app.cipher'] == 'AES-128-CBC' ? 16 : 32,
            )),
        );
    }

    protected function login()
    {
        $this->actingAs(new User);
    }

    protected function buildTheWorld(bool $singleShow = false): void
    {
        $this->app['config']->set(
            'sharp.entities.person',
            $singleShow ? SinglePersonEntity::class : PersonEntity::class,
        );

        $this->app['config']->set(
            'sharp.dashboards.personal_dashboard',
            PersonalDashboardEntity::class,
        );
    }
}
