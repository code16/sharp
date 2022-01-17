<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Auth\SharpEntityPolicy;
use Code16\Sharp\Tests\Fixtures\User;
use Code16\Sharp\Utils\Entities\SharpEntityManager;

class PolicyAuthorizationsTest extends BaseApiTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->login();

        $this->buildTheWorld();

        app(SharpEntityManager::class)
            ->entityFor('person')
            ->setPolicy(AuthorizationsTestPersonPolicy::class);

        app(SharpEntityManager::class)
            ->entityFor('personal_dashboard')
            ->setPolicy(AuthorizationsTestPersonalDashboardPolicy::class);
    }

    /** @test */
    public function we_can_configure_a_policy()
    {
        // Update policy returns true
        $this->postJson('/sharp/api/form/person/1', [])->assertStatus(200);
        $this->getJson('/sharp/api/list/person')->assertStatus(200);

        // Create has no policy, and should therefore return 200
        $this->getJson('/sharp/api/form/person')->assertStatus(200);
        $this->postJson('/sharp/api/form/person', [])->assertStatus(200);

        // Delete policy returns false
        $this->deleteJson('/sharp/api/form/person/50')->assertStatus(403);

        // Update policy with an id > 1 returns 403
        $this->postJson('/sharp/api/form/person/10', [])->assertStatus(403);
    }

    /** @test */
    public function policy_authorizations_are_appended_to_the_response_on_a_form_case()
    {
        $this
            ->getJson('/sharp/api/form/person')
            ->assertJson([
                'authorizations' => [
                    'delete' => false,
                    'update' => true,
                    'create' => true,
                    'view'   => true,
                ],
            ]);

        $this
            ->getJson('/sharp/api/form/person/1')
            ->assertJson([
                'authorizations' => [
                    'delete' => false,
                    'update' => true,
                    'create' => true,
                    'view'   => true,
                ],
            ]);

        $this
            ->getJson('/sharp/api/form/person/10')
            ->assertJson([
                'authorizations' => [
                    'delete' => false,
                    'update' => false,
                    'create' => true,
                    'view'   => true,
                ],
            ]);
    }

    /** @test */
    public function policy_authorizations_are_appended_to_the_response_on_a_list_case()
    {
        $this->getJson('/sharp/api/list/person')->assertJson([
            'authorizations' => [
                'update' => [1],
                'create' => true,
                'view'   => [1, 2],
            ],
        ]);
    }

    /** @test */
    public function global_authorizations_override_policies()
    {
        app(SharpEntityManager::class)
            ->entityFor('person')
            ->setProhibitedActions(['update']);

        $this
            ->getJson('/sharp/api/form/person')
            ->assertJson([
                'authorizations' => [
                    'delete' => false,
                    'update' => false,
                    'create' => true,
                    'view'   => true,
                ],
            ]);
    }

    /** @test */
    public function entity_policy_can_be_set_to_handle_whole_entity_visibility()
    {
        $this->actingAs(new User(['name' => 'Unauthorized-User']));

        $this->getJson('/sharp/api/form/person')->assertStatus(403);
        $this->postJson('/sharp/api/form/person/1', [])->assertStatus(403);
        $this->getJson('/sharp/api/list/person')->assertStatus(403);
        $this->postJson('/sharp/api/form/person', [])->assertStatus(403);
        $this->deleteJson('/sharp/api/form/person/50')->assertStatus(403);
    }

    /** @test */
    public function dashboard_policy_can_be_set_to_handle_whole_dashboard_visibility()
    {
        $this->getJson('/sharp/api/dashboard/personal_dashboard')->assertStatus(200);

        $this->actingAs(new User(['name' => 'Unauthorized-User']));
        $this->getJson('/sharp/api/dashboard/personal_dashboard')->assertStatus(403);
    }
}

class AuthorizationsTestPersonPolicy extends SharpEntityPolicy
{
    public function entity($user): bool
    {
        return $user->name != 'Unauthorized-User';
    }

    public function view($user, $id): bool
    {
        return true;
    }

    public function update($user, $id): bool
    {
        return $id < 2;
    }

    public function delete($user, $id): bool
    {
        return false;
    }
}

class AuthorizationsTestPersonalDashboardPolicy extends SharpEntityPolicy
{
    public function entity($user): bool
    {
        return $user->name != 'Unauthorized-User';
    }
}
