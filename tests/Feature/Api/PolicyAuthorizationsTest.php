<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Tests\Fixtures\User;

class PolicyAuthorizationsTest extends BaseApiTest
{
    protected function getPackageProviders($app)
    {
        $app['config']['sharp.entities.person.policy'] = AuthorizationsTestPersonPolicy::class;

        return parent::getPackageProviders($app);
    }

    /** @test */
    public function we_can_configure_a_policy()
    {
        $this->buildTheWorld();

        // Update policy returns true
        $this->json('post', '/sharp/api/form/person/50', [])->assertStatus(200);
        $this->json('get', '/sharp/api/list/person')->assertStatus(200);

        // Create has no policy, and should therefore return 200
        $this->json('get', '/sharp/api/form/person')->assertStatus(200);
        $this->json('post', '/sharp/api/form/person', [])->assertStatus(200);

        // Delete policy returns false
        $this->json('delete', '/sharp/api/form/person/50')->assertStatus(403);
    }

    /** @test */
    public function policy_authorizations_are_appended_to_the_response()
    {
        $this->buildTheWorld();

        $this->json('get', '/sharp/api/list/person')->assertJson([
            "authorizations" => [
                "delete" => false,
                "update" => true,
                "create" => true,
                "view" => true,
            ]
        ]);
    }

    /** @test */
    public function global_authorizations_override_policies_()
    {
        $this->buildTheWorld();

        $this->app['config']->set(
            'sharp.entities.person.authorizations', [
                "delete" => true,
                "update" => false,
            ]
        );

        $this->json('get', '/sharp/api/list/person')->assertJson([
            "authorizations" => [
                "delete" => true,
                "update" => false,
                "create" => true,
                "view" => true,
            ]
        ]);
    }
}

class AuthorizationsTestPersonPolicy
{
    public function view(User $user, $id) { return true; }

    public function update(User $user, $id) { return true; }

    public function delete(User $user, $id) { return false; }
}