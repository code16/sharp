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
                ], "panel" => [
                    "type" => "panel"
                ]
            ]]);
    }

    /** @test */
    public function we_can_get_dashboard_layout()
    {
        $this->buildTheWorld();

        $this->json('get', '/sharp/api/dashboard')
            ->assertStatus(200)
            ->assertJson(["layout" => [
                "rows" => [
                    [
                        ["key" => "bars", "size" => 12]
                    ], [
                        ["key" => "panel", "size" => 4],
                        ["key" => "bars2", "size" => 8]
                    ]
                ]
            ]]);
    }

    /** @test */
    public function we_can_get_dashboard_data()
    {
        $this->buildTheWorld();

        $this->json('get', '/sharp/api/dashboard')
            ->assertStatus(200)
            ->assertJson(["data" => [
                "bars1" => [
                    "key" => "bars1",
                    "datasets" => [
                        [
                            "values" => [10, 20, 30],
                            "label" => "Bars 1"
                        ]
                    ],
                    "labels" => ["a", "b", "c"]
                ],
                "bars2" => [
                    "key" => "bars2",
                    "datasets" => [
                        [
                            "values" => [10, 20, 30],
                            "label" => "Bars 2"
                        ]
                    ],
                    "labels" => ["a", "b", "c"],
                ],
                "panel" => [
                    "key" => "panel",
                    "data" => [
                        "name" => "John Wayne"
                    ]
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