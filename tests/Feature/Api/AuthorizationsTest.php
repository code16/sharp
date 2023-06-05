<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Tests\Fixtures\PersonSharpForm;
use Code16\Sharp\Utils\Entities\SharpEntityManager;

class AuthorizationsTest extends BaseApiTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->login();
        $this->buildTheWorld();
    }

    /** @test */
    public function we_can_configure_prohibited_actions_on_entities()
    {
        app(SharpEntityManager::class)
            ->entityFor('person')
            ->setProhibitedActions(['delete', 'create', 'update', 'view']);

        $this->postJson('/sharp/api/form/person/50', [])->assertForbidden();
        $this->postJson('/sharp/api/form/person', [])->assertForbidden();
        $this->deleteJson('/sharp/api/show/person/50')->assertForbidden();
        $this->getJson('/sharp/api/form/person')->assertForbidden();

        $this->getJson('/sharp/api/show/person/50')->assertForbidden();
        $this->getJson('/sharp/api/form/person/50')->assertForbidden();

        // We can still view the list
        $this->json('get', '/sharp/api/list/person')->assertOk();
    }

    /** @test */
    public function we_can_access_form_in_readonly_mode_if_there_is_no_show()
    {
        app(SharpEntityManager::class)
            ->entityFor('person')
            ->setShow(null)
            ->setProhibitedActions(['update']);

        $this->getJson('/sharp/api/form/person')->assertOk();
        $this->postJson('/sharp/api/form/person/50', [])->assertForbidden();
    }

    /** @test */
    public function default_prohibited_actions_on_entity_is_handled()
    {
        app(SharpEntityManager::class)
            ->entityFor('person')
            ->setProhibitedActions(['delete', 'update']);

        $this->getJson('/sharp/api/list/person')->assertOk();
        $this->getJson('/sharp/api/show/person/50')->assertOk();
        $this->getJson('/sharp/api/form/person')->assertOk();
        $this->getJson('/sharp/api/form/person/50')->assertForbidden();
        $this->postJson('/sharp/api/form/person', [])->assertOk();
        $this->postJson('/sharp/api/form/person/50', [])->assertForbidden();
        $this->deleteJson('/sharp/api/show/person/50')->assertForbidden();
    }

    /** @test */
    public function prohibited_actions_are_appended_to_the_response_on_a_form_get_request()
    {
        app(SharpEntityManager::class)
            ->entityFor('person')
            ->setProhibitedActions(['delete', 'update']);

        // Create (no instanceId, only create is allowed)
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

        // Show
        $this->getJson('/sharp/api/show/person/1')->assertJson([
            'authorizations' => [
                'delete' => false,
                'update' => false,
                'create' => true,
                'view' => true,
            ],
        ]);
    }

    /** @test */
    public function prohibited_actions_are_appended_to_the_response_on_a_list_get_request()
    {
        app(SharpEntityManager::class)
            ->entityFor('person')
            ->setProhibitedActions(['delete', 'update']);

        $this
            ->getJson('/sharp/api/list/person')
            ->assertJson([
                'authorizations' => [
                    'update' => false,
                    'create' => true,
                    'view' => [1, 2],
                ],
            ]);
    }

    /** @test */
    public function actions_are_not_prohibited_by_default()
    {
        $this->withoutExceptionHandling();

        // Create (no instanceId, only create is allowed)
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

        // Edit
        $this
            ->getJson('/sharp/api/form/person/1')
            ->assertJson([
                'authorizations' => [
                    'delete' => true,
                    'update' => true,
                    'create' => true,
                    'view' => true,
                ],
            ]);

        // List
        $this
            ->getJson('/sharp/api/list/person')
            ->assertJson([
                'authorizations' => [
                    'update' => true,
                    'create' => true,
                    'view' => true,
                ],
            ]);
    }

    /** @test */
    public function a_sub_entity_takes_its_main_entity_prohibited_actions()
    {
        app(SharpEntityManager::class)
            ->entityFor('person')
            ->setMultiforms([
                'big' => [PersonSharpForm::class],
            ])
            ->setProhibitedActions(['delete']);

        $this->postJson('/sharp/api/form/person:big/50', [])->assertOk();
        $this->postJson('/sharp/api/form/person:big', [])->assertOk();
        $this->deleteJson('/sharp/api/show/person:big/50')->assertForbidden();
        $this->getJson('/sharp/api/form/person:big')->assertOk();
        $this->getJson('/sharp/api/form/person:big/50')->assertOk();
        $this->getJson('/sharp/api/list/person')->assertOk();
    }
}
