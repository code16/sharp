<?php

namespace Code16\Sharp\Tests\Feature\Api;

class BreadcrumbTest extends BaseApiTest
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
}