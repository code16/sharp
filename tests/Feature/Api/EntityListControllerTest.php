<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Tests\Fixtures\PersonSharpForm;

class EntityListControllerTest extends BaseApiTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->login();
    }

    /** @test */
    public function we_can_get_list_data_for_an_entity()
    {
        $this->buildTheWorld();

        $this->json('get', '/sharp/api/list/person')
            ->assertStatus(200)
            ->assertJson(["data" => [
                "items" => [
                    ["id" => 1, "name" => "John <b>Wayne</b>", "age" => 22],
                    ["id" => 2, "name" => "Mary <b>Wayne</b>", "age" => 26],
                ]
            ]]);
    }

    /** @test */
    public function we_can_get_paginated_list_data_for_an_entity()
    {
        $this->buildTheWorld();

        $this->json('get', '/sharp/api/list/person?paginated=1')
            ->assertStatus(200)
            ->assertJsonFragment(["data" => [
                "items" => [
                    ["id" => 1, "name" => "John <b>Wayne</b>", "age" => 22],
                    ["id" => 2, "name" => "Mary <b>Wayne</b>", "age" => 26],
                ], "page" => 1, "totalCount" => 20, "pageSize" => 2
            ]]);
    }

    /** @test */
    public function we_can_search_for_an_instance()
    {
        $this->withoutExceptionHandling();
        $this->buildTheWorld();

        $this->json('get', '/sharp/api/list/person?search=john')
            ->assertStatus(200)
            ->assertJsonFragment(["data" => [
                "items" => [
                    ["id" => 1, "name" => "John <b>Wayne</b>", "age" => 22],
                ]
            ]]);
    }

    /** @test */
    public function we_wont_get_entity_attribute_for_a_non_form_data()
    {
        $this->buildTheWorld();

        $result = $this->json('get', '/sharp/api/list/person');

        $this->assertArrayNotHasKey("job", $result->json()["data"]["items"][0]);
    }

    /** @test */
    public function we_can_get_data_containers_for_an_entity()
    {
        $this->buildTheWorld();

        $this->json('get', '/sharp/api/list/person')
            ->assertStatus(200)
            ->assertJson(["containers" => [
                "name" => [
                    "key" => "name",
                    "label" => "Name",
                    "sortable" => true,
                    "html" => true
                ], "age" => [
                    "key" => "age",
                    "label" => "Age",
                    "sortable" => true,
                ]
            ]]);
    }

    /** @test */
    public function we_can_get_list_layout_for_an_entity()
    {
        $this->buildTheWorld();

        $this->json('get', '/sharp/api/list/person')
            ->assertStatus(200)
            ->assertJson(["layout" => [
                [
                    "key" => "name",
                    "size" => 6,
                    "sizeXS" => 12
                ], [
                    "key" => "age",
                    "size" => 6,
                    "hideOnXS" => true
                ]
            ]]);
    }

    /** @test */
    public function we_can_get_list_config_for_an_entity()
    {
        $this->buildTheWorld();

        $this->json('get', '/sharp/api/list/person')
            ->assertStatus(200)
            ->assertJson(["config" => [
                "instanceIdAttribute" => "id",
                "searchable" => true,
                "paginated" => false
            ]]);
    }

    /** @test */
    public function we_can_get_notifications()
    {
        $this->buildTheWorld();

        (new PersonSharpForm())->notify("title")
            ->setLevelSuccess()
            ->setDetail("body")
            ->setAutoHide(false);

        $this->json('get', '/sharp/api/list/person')
            ->assertStatus(200)
            ->assertJson(["notifications" => [[
                "level" => "success",
                "title" => "title",
                "message" => "body",
                "autoHide" => false
            ]]]);

        $this->json('get', '/sharp/api/list/person')
            ->assertStatus(200)
            ->assertJsonMissing(["alert"]);

        (new PersonSharpForm())->notify("title1");
        (new PersonSharpForm())->notify("title2");

        $this->json('get', '/sharp/api/list/person')
            ->assertStatus(200)
            ->assertJson(["notifications" => [[
                "title" => "title1",
            ], [
                "title" => "title2",
            ]]]);
    }

    /** @test */
    public function invalid_entity_key_is_returned_as_404()
    {
        $this->buildTheWorld();

        $this->json('get', '/sharp/api/list/notanvalidentity')
            ->assertStatus(404);
    }

    public function we_can_reorder_instances()
    {
        $this->buildTheWorld();

        $this->json('post', '/sharp/api/list/person/reorder', [
            3,2,1
        ])->assertStatus(200);
    }
}