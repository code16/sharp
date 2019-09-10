<?php

namespace Code16\Sharp\Tests\Feature\Api;

class DashboardControllerTest extends BaseApiTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    /** @test */
    public function we_can_get_dashboard_widgets()
    {
        $this->withoutExceptionHandling();
        $this->buildTheWorld();

        $this->getJson(route('code16.sharp.api.dashboard', 'personal_dashboard'))
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

        $this->getJson(route('code16.sharp.api.dashboard', 'personal_dashboard'))
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

        $this->getJson(route('code16.sharp.api.dashboard', 'personal_dashboard'))
            ->assertStatus(200)
            ->assertJson(["data" => [
                "bars1" => [
                    "key" => "bars1",
                    "datasets" => [
                        [
                            "data" => [10, 20, 30],
                            "label" => "Bars 1"
                        ]
                    ],
                    "labels" => ["a", "b", "c"]
                ],
                "bars2" => [
                    "key" => "bars2",
                    "datasets" => [
                        [
                            "data" => [10, 20, 30],
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
}