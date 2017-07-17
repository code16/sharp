<?php

namespace Code16\Sharp\Tests\Feature\Api;

class DashboardControllerTest extends BaseApiTest
{
    protected function setUp()
    {
        parent::setUp();
        $this->login();
    }

    /** @test */
    public function we_can_get_dashboard_widgets()
    {
        $this->buildTheWorld();

        $this->json('get', '/sharp/api/dashboard')
            ->assertStatus(200)
            ->assertJson(["widgets" => [
                "bars" => [
                    "type" => "graph"
                ]
            ]]);
    }

    /** @test */
    public function we_get_a_404_if_no_dashboard_is_configured()
    {
        $this->buildTheWorld();

        $this->app['config']->set(
            'sharp.dashboard',
            ''
        );

        $this->json('get', '/sharp/api/dashboard')
            ->assertStatus(404);
    }

}