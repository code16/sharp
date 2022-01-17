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

        $this->postJson('/sharp/api/form/person/50', [])->assertStatus(403);
        $this->postJson('/sharp/api/form/person', [])->assertStatus(403);
        $this->deleteJson('/sharp/api/form/person/50')->assertStatus(403);
        $this->getJson('/sharp/api/form/person')->assertStatus(403);

        // Can't see the form, since view is false
        $this->getJson('/sharp/api/form/person/50')->assertStatus(403);

        // We can still view the list
        $this->json('get', '/sharp/api/list/person')->assertStatus(200);
    }

    /** @test */
    public function default_prohibited_actions_on_entity_is_handled()
    {
        app(SharpEntityManager::class)
            ->entityFor('person')
            ->setProhibitedActions(['delete', 'update']);

        $this->getJson('/sharp/api/list/person')->assertStatus(200);
        $this->getJson('/sharp/api/form/person')->assertStatus(200);
        $this->getJson('/sharp/api/form/person/50')->assertStatus(200);
        $this->postJson('/sharp/api/form/person', [])->assertStatus(200);
        $this->postJson('/sharp/api/form/person/50', [])->assertStatus(403);
        $this->deleteJson('/sharp/api/form/person/50')->assertStatus(403);
    }

    /** @test */
    public function prohibited_actions_are_appended_to_the_response_on_a_form_get_request()
    {
        app(SharpEntityManager::class)
            ->entityFor('person')
            ->setProhibitedActions(['delete', 'update']);

        // Create
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

        // Edit
        $this->getJson('/sharp/api/form/person/1')->assertJson([
            'authorizations' => [
                'delete' => false,
                'update' => false,
                'create' => true,
                'view'   => true,
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
                    'view'   => [1, 2],
                ],
            ]);
    }

    /** @test */
    public function prohibited_actions_are_true_by_default()
    {
        $this->withoutExceptionHandling();

        // Create
        $this
            ->getJson('/sharp/api/form/person')
            ->assertJson([
                'authorizations' => [
                    'delete' => true,
                    'update' => true,
                    'create' => true,
                    'view'   => true,
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
                    'view'   => true,
                ],
            ]);

        // List
        $this
            ->getJson('/sharp/api/list/person')
            ->assertJson([
                'authorizations' => [
                    'update' => true,
                    'create' => true,
                    'view'   => true,
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

        $this->postJson('/sharp/api/form/person:big/50', [])->assertStatus(200);
        $this->postJson('/sharp/api/form/person:big', [])->assertStatus(200);
        $this->json('delete', '/sharp/api/form/person:big/50')->assertStatus(403);
        $this->getJson('/sharp/api/form/person:big')->assertStatus(200);
        $this->getJson('/sharp/api/form/person:big/50')->assertStatus(200);
        $this->getJson('/sharp/api/list/person')->assertStatus(200);
    }
}
