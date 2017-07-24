<?php

namespace Code16\Sharp\Tests\Feature\Api;

class AuthorizationsTest extends BaseApiTest
{

    /** @test */
    public function we_can_configure_global_authorizations_on_entities()
    {
        $this->buildTheWorld();
        $this->login();

        $this->app['config']->set(
            'sharp.entities.person.authorizations', [
                "delete" => false,
                "create" => false,
                "update" => false,
                "view" => false,
            ]
        );

        $this->json('post', '/sharp/api/form/person/50', [])->assertStatus(403);
        $this->json('post', '/sharp/api/form/person', [])->assertStatus(403);
        $this->json('delete', '/sharp/api/form/person/50')->assertStatus(403);
        $this->json('get', '/sharp/api/form/person')->assertStatus(403);

        // Can't neither see the form, since view is false
        $this->json('get', '/sharp/api/form/person/50')->assertStatus(403);


        // We can still view the list
        $this->json('get', '/sharp/api/list/person')->assertStatus(200);
    }

    /** @test */
    public function default_global_authorizations_on_entity_is_handled()
    {
        $this->buildTheWorld();
        $this->login();

        $this->app['config']->set(
            'sharp.entities.person.authorizations', [
                "delete" => false,
                "update" => false,
            ]
        );

        $this->json('get', '/sharp/api/list/person')->assertStatus(200);
        $this->json('get', '/sharp/api/form/person')->assertStatus(200);
        $this->json('get', '/sharp/api/form/person/50')->assertStatus(200);
        $this->json('post', '/sharp/api/form/person', [])->assertStatus(200);
        $this->json('post', '/sharp/api/form/person/50', [])->assertStatus(403);
        $this->json('delete', '/sharp/api/form/person/50')->assertStatus(403);
    }

    /** @test */
    public function global_authorizations_are_appended_to_the_response_on_a_form_get_request()
    {
        $this->buildTheWorld();
        $this->login();

        $this->app['config']->set(
            'sharp.entities.person.authorizations', [
                "delete" => false,
                "update" => false,
            ]
        );

        // Create
        $this->json('get', '/sharp/api/form/person')->assertJson([
            "authorizations" => [
                "delete" => false,
                "update" => false,
                "create" => true,
                "view" => true,
            ]
        ]);

        // Edit
        $this->json('get', '/sharp/api/form/person/1')->assertJson([
            "authorizations" => [
                "delete" => false,
                "update" => false,
                "create" => true,
                "view" => true,
            ]
        ]);
    }

    /** @test */
    public function global_authorizations_are_appended_to_the_response_on_a_list_get_request()
    {
        $this->buildTheWorld();
        $this->login();

        $this->app['config']->set(
            'sharp.entities.person.authorizations', [
                "delete" => false,
                "update" => false,
            ]
        );

        $this->json('get', '/sharp/api/list/person')->assertJson([
            "authorizations" => [
                "update" => false,
                "create" => true,
                "view" => true,
            ]
        ]);
    }

    /** @test */
    public function global_authorizations_are_true_by_default()
    {
        $this->buildTheWorld();
        $this->login();

        // Create
        $this->json('get', '/sharp/api/form/person')->assertJson([
            "authorizations" => [
                "delete" => true,
                "update" => true,
                "create" => true,
                "view" => true,
            ]
        ]);

        // Edit
        $this->json('get', '/sharp/api/form/person/1')->assertJson([
            "authorizations" => [
                "delete" => true,
                "update" => true,
                "create" => true,
                "view" => true,
            ]
        ]);

        // List
        $this->json('get', '/sharp/api/list/person')->assertJson([
            "authorizations" => [
                "update" => true,
                "create" => true,
                "view" => true,
            ]
        ]);
    }

}