<?php

namespace Code16\Sharp\Tests\Feature\Api;

class ApiControllerTest extends BaseApiTest
{
    /** @test */
    public function we_can_show_an_entity_data()
    {
        $this->buildTheWorld();

        $this->json('get', '/sharp/api/form/person/1')
            ->assertStatus(200)
            ->assertJson(["data" => [
                "name" => "John Wayne"
            ]]);
    }

    /** @test */
    public function we_can_update_an_entity()
    {
        $this->buildTheWorld();

        $this->json('post', '/sharp/api/form/person/1', [
            "name" => "Jane Fonda"
        ])->assertStatus(200)
            ->assertJson(["ok" => true]);
    }

    /** @test */
    public function we_can_validate_an_entity_before_update()
    {
        $this->buildTheWorld(true);

        $this->json('post', '/sharp/api/form/person/1', [
            "age" => 22
        ])->assertStatus(422)
            ->assertJson([
                "name" => [
                    "The name field is required."
                ]
            ]);
    }

    /** @test */
    public function we_can_store_a_new_entity()
    {
        $this->buildTheWorld();

        $this->json('post', '/sharp/api/form/person', [
            "name" => "Jane Fonda"
        ])->assertStatus(200)
            ->assertJson(["ok" => true]);
    }

    /** @test */
    public function applicative_exception_is_returned_as_417()
    {
        $this->buildTheWorld();

        $this->json('post', '/sharp/api/form/person/notanid', [
            "name" => "Jane Fonda"
        ])->assertStatus(417)
            ->assertJson([
                "message" => "notanid is not a valid id"
            ]);
    }
}