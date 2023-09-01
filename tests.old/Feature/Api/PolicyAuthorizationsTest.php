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
        $this->postJson('/sharp/api/form/person/1', [])->assertOk();
        $this->getJson('/sharp/api/list/person')->assertOk();

        // Create has no policy, and should therefore return 200
        $this->getJson('/sharp/api/form/person')->assertOk();
        $this->postJson('/sharp/api/form/person', [])->assertOk();

        // Delete policy returns false
        $this->deleteJson('/sharp/api/show/person/50')->assertForbidden();

        // Update policy with an id > 1 returns 403
        $this->postJson('/sharp/api/form/person/10', [])->assertForbidden();
    }

    /** @test */
    public function policy_authorizations_are_appended_to_the_response_on_a_create_or_show_case()
    {
        $this
            ->getJson('/sharp/api/form/person')
            ->assertJson([
                'authorizations' => [
                    'delete' => false,
                    'update' => false,
                    'create' => true,
                    'view' => false,
                ],
            ]);

        $this
            ->getJson('/sharp/api/show/person/1')
            ->assertJson([
                'authorizations' => [
                    'delete' => false,
                    'update' => true,
                    'create' => true,
                    'view' => true,
                ],
            ]);

        $this
            ->getJson('/sharp/api/show/person/10')
            ->assertJson([
                'authorizations' => [
                    'delete' => false,
                    'update' => false,
                    'create' => true,
                    'view' => true,
                ],
            ]);
    }

    /** @test */
    public function view_policy_applies_to_show_if_defined_and_form_edit_in_readonly_mode_if_not()
    {
        // There is a show page by default:
        $this->getJson('/sharp/api/show/person/50')->assertOk();
        $this->getJson('/sharp/api/form/person/50')->assertForbidden();

        // Without show page, form edit become accessible:
        app(SharpEntityManager::class)->entityFor('person')->setShow(null);
        $this->getJson('/sharp/api/form/person/50')->assertOk();
    }

    /** @test */
    public function policy_authorizations_are_appended_to_the_response_on_a_list_case()
    {
        $this->getJson('/sharp/api/list/person')->assertJson([
            'authorizations' => [
                'update' => [1],
                'create' => true,
                'view' => [1, 2],
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
            ->getJson('/sharp/api/show/person/1')
            ->assertJson([
                'authorizations' => [
                    'delete' => false,
                    'update' => false,
                    'create' => true,
                    'view' => true,
                ],
            ]);
    }

    /** @test */
    public function entity_policy_can_be_set_to_handle_whole_entity_visibility()
    {
        $this->actingAs(new User(['name' => 'Unauthorized-User']));

        $this->getJson('/sharp/api/form/person')->assertForbidden();
        $this->postJson('/sharp/api/form/person/1', [])->assertForbidden();
        $this->getJson('/sharp/api/list/person')->assertForbidden();
        $this->postJson('/sharp/api/form/person', [])->assertForbidden();
        $this->deleteJson('/sharp/api/show/person/50')->assertForbidden();
    }

    /** @test */
    public function dashboard_policy_can_be_set_to_handle_whole_dashboard_visibility()
    {
        $this->actingAs(new User(['name' => 'Unauthorized-User']));
        $this->getJson('/sharp/api/dashboard/personal_dashboard')->assertForbidden();
    }

    /** @test */
    public function view_and_update_and_delete_policies_are_not_checked_on_create_case()
    {
        app(SharpEntityManager::class)
            ->entityFor('person')
            ->setPolicy(AuthorizationsTestPersonWithExceptionsPolicy::class);

        $this
            ->getJson('/sharp/api/form/person')
            ->assertJson([
                'authorizations' => [
                    'delete' => false,
                    'update' => false,
                    'create' => true,
                    'view' => false,
                ],
            ]);
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

class AuthorizationsTestPersonWithExceptionsPolicy extends SharpEntityPolicy
{
    public function view($user, $id): bool
    {
        throw new \Exception('nope');
    }

    public function update($user, $id): bool
    {
        throw new \Exception('nope');
    }

    public function delete($user, $id): bool
    {
        throw new \Exception('nope');
    }
}
