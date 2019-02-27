<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Tests\Fixtures\User;

class PolicyAuthorizationsTest extends BaseApiTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']['sharp.entities.person.policy'] = AuthorizationsTestPersonPolicy::class;
        app()['config']['sharp.dashboards.personal_dashboard.policy'] = AuthorizationsTestPersonalDashboardPolicy::class;
    }

    /** @test */
    public function we_can_configure_a_policy()
    {
        $this->buildTheWorld();

        // Update policy returns true
        $this->json('post', '/sharp/api/form/person/1', [])->assertStatus(200);
        $this->json('get', '/sharp/api/list/person')->assertStatus(200);

        // Create has no policy, and should therefore return 200
        $this->json('get', '/sharp/api/form/person')->assertStatus(200);
        $this->json('post', '/sharp/api/form/person', [])->assertStatus(200);

        // Delete policy returns false
        $this->json('delete', '/sharp/api/form/person/50')->assertStatus(403);

        // Update policy with an id > 1 returns 403
        $this->json('post', '/sharp/api/form/person/10', [])->assertStatus(403);
    }

    /** @test */
    public function policy_authorizations_are_appended_to_the_response_on_a_form_case()
    {
        $this->buildTheWorld();

        $this->json('get', '/sharp/api/form/person')->assertJson([
            "authorizations" => [
                // Delete policy is false, but in a create case it will return true
                // because there is no entity to check for.
                "delete" => true,
                "update" => true,
                "create" => true,
                "view" => true,
            ]
        ]);

        $this->json('get', '/sharp/api/form/person/1')->assertJson([
            "authorizations" => [
                "delete" => false,
                "update" => true,
                "create" => true,
                "view" => true,
            ]
        ]);

        $this->json('get', '/sharp/api/form/person/10')->assertJson([
            "authorizations" => [
                "delete" => false,
                "update" => false,
                "create" => true,
                "view" => true,
            ]
        ]);
    }

    /** @test */
    public function policy_authorizations_are_appended_to_the_response_on_a_list_case()
    {
        $this->buildTheWorld();

        $this->json('get', '/sharp/api/list/person')->assertJson([
            "authorizations" => [
                "update" => [1],
                "create" => true,
                "view" => [1,2],
            ]
        ]);
    }

    /** @test */
    public function global_authorizations_override_policies()
    {
        $this->buildTheWorld();

        $this->app['config']->set(
            'sharp.entities.person.authorizations', [
                "delete" => true,
                "update" => false,
            ]
        );

        $this->json('get', '/sharp/api/form/person')->assertJson([
            "authorizations" => [
                "delete" => true,
                "update" => false,
                "create" => true,
                "view" => true,
            ]
        ]);
    }

    /** @test */
    public function entity_policy_can_be_set_to_handle_whole_entity_visibility()
    {
        $this->buildTheWorld();
        $this->actingAs(new User(["name" => "Unauthorized-User"]));

        $this->json('get', '/sharp/api/form/person')->assertStatus(403);
        $this->json('post', '/sharp/api/form/person/1', [])->assertStatus(403);
        $this->json('get', '/sharp/api/list/person')->assertStatus(403);
        $this->json('post', '/sharp/api/form/person', [])->assertStatus(403);
        $this->json('delete', '/sharp/api/form/person/50')->assertStatus(403);
    }

    /** @test */
    public function dashboard_policy_can_be_set_to_handle_whole_dashboard_visibility()
    {
        $this->buildTheWorld();

        $this->json('get', '/sharp/api/dashboard/personal_dashboard')->assertStatus(200);

        $this->actingAs(new User(["name" => "Unauthorized-User"]));
        $this->json('get', '/sharp/api/dashboard/personal_dashboard')->assertStatus(403);
    }
}

class AuthorizationsTestPersonPolicy
{
    public function entity(User $user)
    {
        return $user->name != "Unauthorized-User";
    }

    public function view(User $user, $id) { return true; }

    public function update(User $user, $id) { return $id < 2; }

    public function delete(User $user, $id) { return false; }
}

class AuthorizationsTestPersonalDashboardPolicy
{
    public function view(User $user)
    {
        return $user->name != "Unauthorized-User";
    }
}