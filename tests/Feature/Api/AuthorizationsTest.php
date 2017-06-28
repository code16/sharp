<?php

namespace Code16\Sharp\Tests\Feature\Api;

class AuthorizationsTest extends BaseApiTest
{
    /** @test */
    public function unauthenticated_user_wont_pass_on_an_api_call()
    {
        $this->buildTheWorld(false, false);

        $this->json('get', '/sharp/api/list/person')->assertStatus(401);
    }

    /** @test */
    public function unauthenticated_user_are_redirected_on_a_web_call()
    {
        $this->buildTheWorld(false, false);

        $this->get('/sharp/list/person')->assertStatus(302);
    }

    /** @test */
    public function we_can_configure_global_authorizations()
    {
        $this->buildTheWorld();

        $this->app['config']->set(
            'sharp.entities.person.authorizations', [
                "delete" => false,
                "create" => false,
                "update" => false,
                "view" => false,
            ]
        );

        $this->json('post', '/sharp/api/form/person/50', [])->assertStatus(403);
        $this->json('get', '/sharp/api/list/person')->assertStatus(403);
        $this->json('get', '/sharp/api/form/person')->assertStatus(403);
        $this->json('post', '/sharp/api/form/person', [])->assertStatus(403);
        $this->json('delete', '/sharp/api/form/person/50')->assertStatus(403);
    }

    /** @test */
    public function default_global_authorizations_is_handled()
    {
        $this->buildTheWorld();

        $this->app['config']->set(
            'sharp.entities.person.authorizations', [
                "delete" => false,
                "update" => false,
            ]
        );

        $this->json('get', '/sharp/api/list/person')->assertStatus(200);
        $this->json('get', '/sharp/api/form/person')->assertStatus(200);
        $this->json('post', '/sharp/api/form/person', [])->assertStatus(200);
        $this->json('post', '/sharp/api/form/person/50', [])->assertStatus(403);
        $this->json('delete', '/sharp/api/form/person/50')->assertStatus(403);
    }

    /** @test */
    public function authorizations_are_appended_to_the_response()
    {
        $this->buildTheWorld();

        $this->app['config']->set(
            'sharp.entities.person.authorizations', [
                "delete" => false,
                "update" => false,
            ]
        );

        $this->json('get', '/sharp/api/list/person')->assertJson([
            "authorizations" => [
                "delete" => false,
                "update" => false,
                "create" => true,
                "view" => true,
            ]
        ]);
    }
}