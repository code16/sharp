<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Tests\Fixtures\PersonSharpShow;

class ShowControllerTest extends BaseApiTest
{

    protected function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    /** @test */
    public function we_can_get_show_data_for_an_entity()
    {
        $this->withoutExceptionHandling();
        $this->buildTheWorld();

        $this->getJson('/sharp/api/show/person/1')
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "name" => "John Wayne"
                ]
            ]);
    }

    /** @test */
    public function we_wont_get_entity_attribute_for_a_non_show_data()
    {
        $this->buildTheWorld();

        $result = $this->getJson('/sharp/api/show/person/1');

        $this->assertArrayHasKey("name", $result->json()["data"]);
        $this->assertArrayNotHasKey("job", $result->json()["data"]);
    }

    /** @test */
    public function we_can_get_show_fields_for_an_entity()
    {
        $this->buildTheWorld();

        $this->json('get', '/sharp/api/show/person/1')
            ->assertStatus(200)
            ->assertJson(["fields" => [
                "name" => [
                    "type" => "text"
                ]
            ]]);
    }

    protected function buildTheWorld()
    {
        parent::buildTheWorld();

        $this->app['config']->set(
            'sharp.entities.person.show',
            PersonSharpShow::class
        );
    }
}