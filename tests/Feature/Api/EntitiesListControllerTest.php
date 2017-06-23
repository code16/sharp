<?php

namespace Code16\Sharp\Tests\Feature\Api;

class EntitiesListControllerTest extends BaseApiTest
{
    /** @test */
    public function we_can_get_list_data_for_an_entity()
    {
        $this->buildTheWorld();

        $this->json('get', '/sharp/api/list/person')
            ->assertStatus(200)
            ->assertJson(["data" => [
                [/*"id" => 1, */"name" => "John <b>Wayne</b>", "age" => 22],
                [/*"id" => 2, */"name" => "Mary <b>Wayne</b>", "age" => 26],
            ]]);
    }

    /** @test */
    public function we_wont_get_entity_attribute_for_a_non_form_data()
    {
        $this->buildTheWorld();

        $result = $this->json('get', '/sharp/api/list/person');

        $this->assertArrayNotHasKey("job", $result->json()["data"][0]);
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
//
//    /** @test */
//    public function applicative_exception_is_returned_as_417()
//    {
//        $this->buildTheWorld();
//
//        $this->json('post', '/sharp/api/form/person/notanid', [
//            "name" => "Jane Fonda"
//        ])->assertStatus(417)
//            ->assertJson([
//                "message" => "notanid is not a valid id"
//            ]);
//    }
}