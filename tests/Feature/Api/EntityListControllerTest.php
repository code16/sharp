<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Tests\Fixtures\PersonSharpForm;

class EntityListControllerTest extends BaseApiTest
{
    protected function setUp()
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
    public function we_can_search_for_an_entity()
    {
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
    public function we_can_filter_entities()
    {
        $this->buildTheWorld();

        $this->json('get', '/sharp/api/list/person?filter_age=22')
            ->assertStatus(200)
            ->assertJsonFragment(["data" => [
                "items" => [
                    ["id" => 1, "name" => "John <b>Wayne</b>", "age" => 22]
                ]
            ]]);
    }

    /** @test */
    public function default_filter_value_is_used_if_no_value_was_sent()
    {
        $this->buildTheWorld();

        // We use a special QS key "default_age" only for test purpose
        // to know that we should use default value in this case
        $this->json('get', '/sharp/api/list/person?default_age=true')
            ->assertStatus(200)
            ->assertJsonFragment(["data" => [
                "items" => [
                    ["id" => 1, "name" => "John <b>Wayne</b>", "age" => 22]
                ]
            ]]);
    }

    /** @test */
    public function we_can_filter_with_multiple_values_on_entities()
    {
        $this->buildTheWorld();

        $this->json('get', '/sharp/api/list/person?filter_age_multiple=22,26')
            ->assertStatus(200)
            ->assertJsonFragment(["data" => [
                "items" => [
                    ["id" => 1, "name" => "John <b>Wayne</b>", "age" => 22],
                    ["id" => 2, "name" => "Mary <b>Wayne</b>", "age" => 26]
                ]
            ]]);
    }

    /** @test */
    public function we_can_filter_on_a_single_value_with_a_multiple_values_filter()
    {
        $this->buildTheWorld();

        $this->json('get', '/sharp/api/list/person?filter_age_multiple=22')
            ->assertStatus(200)
            ->assertJsonFragment(["data" => [
                "items" => [
                    ["id" => 1, "name" => "John <b>Wayne</b>", "age" => 22],
                ]
            ]]);
    }

    /** @test */
    public function we_can_define_a_was_set_callback_on_a_filter()
    {
        $this->buildTheWorld();

        $age = rand(1, 99);
        $this->json('get', '/sharp/api/list/person?filter_age=' . $age);

        // The age was put in session in the Callback
        $this->assertEquals($age, session("filter_age_was_set"));
    }

    /** @test */
    public function we_can_force_a_filter_value_in_a_callback()
    {
        $this->buildTheWorld();

        // Filter `age` will be force set in the `age_forced` filter callback
        $this->json('get', '/sharp/api/list/person?filter_age_forced=22&filter_age=12')
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
                "displayMode" => "list",
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