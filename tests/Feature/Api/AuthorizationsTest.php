<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Tests\Fixtures\PersonSharpForm;

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

        $this->postJson('/sharp/api/form/person/50', [])->assertStatus(403);
        $this->postJson('/sharp/api/form/person', [])->assertStatus(403);
        $this->deleteJson('/sharp/api/form/person/50')->assertStatus(403);
        $this->getJson('/sharp/api/form/person')->assertStatus(403);

        // Can't neither see the form, since view is false
        $this->getJson('/sharp/api/form/person/50')->assertStatus(403);


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

        $this->getJson('/sharp/api/list/person')->assertStatus(200);
        $this->getJson('/sharp/api/form/person')->assertStatus(200);
        $this->getJson('/sharp/api/form/person/50')->assertStatus(200);
        $this->postJson('/sharp/api/form/person', [])->assertStatus(200);
        $this->postJson('/sharp/api/form/person/50', [])->assertStatus(403);
        $this->deleteJson('/sharp/api/form/person/50')->assertStatus(403);
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
        $this->getJson('/sharp/api/form/person')->assertJson([
            "authorizations" => [
                "delete" => false,
                "update" => false,
                "create" => true,
                "view" => true,
            ]
        ]);

        // Edit
        $this->getJson('/sharp/api/form/person/1')->assertJson([
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

        $this->getJson('/sharp/api/list/person')->assertJson([
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
        $this->withoutExceptionHandling();
        $this->buildTheWorld();
        $this->login();

        // Create
        $this->getJson('/sharp/api/form/person')->assertJson([
            "authorizations" => [
                "delete" => true,
                "update" => true,
                "create" => true,
                "view" => true,
            ]
        ]);

        // Edit
        $this->getJson('/sharp/api/form/person/1')->assertJson([
            "authorizations" => [
                "delete" => true,
                "update" => true,
                "create" => true,
                "view" => true,
            ]
        ]);

        // List
        $this->getJson('/sharp/api/list/person')->assertJson([
            "authorizations" => [
                "update" => true,
                "create" => true,
                "view" => true,
            ]
        ]);
    }

    /** @test */
    public function a_sub_entity_takes_its_main_entity_global_authorizations()
    {
        $this->withoutExceptionHandling();
        $this->buildTheWorld();
        $this->login();

        $this->app['config']->set(
            'sharp.entities.person.form', null
        );

        $this->app['config']->set(
            'sharp.entities.person.forms.big.form', PersonSharpForm::class
        );

        $this->app['config']->set(
            'sharp.entities.person.authorizations', [
                "delete" => false,
                "create" => true,
                "update" => true,
                "view" => true,
            ]
        );

        $this->postJson('/sharp/api/form/person:big/50', [])->assertStatus(200);
        $this->postJson('/sharp/api/form/person:big', [])->assertStatus(200);
//        $this->json('delete', '/sharp/api/form/person:big/50')->assertStatus(403);
        $this->getJson('/sharp/api/form/person:big')->assertStatus(200);
        $this->getJson('/sharp/api/form/person:big/50')->assertStatus(200);
        $this->getJson('/sharp/api/list/person')->assertStatus(200);
    }

}