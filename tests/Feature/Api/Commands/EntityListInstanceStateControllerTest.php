<?php

namespace Code16\Sharp\Tests\Feature\Api\Commands;

use Code16\Sharp\EntityList\Commands\EntityState;
use Code16\Sharp\Exceptions\Form\SharpApplicativeException;
use Code16\Sharp\Tests\Feature\Api\BaseApiTest;
use Code16\Sharp\Tests\Fixtures\PersonSharpEntityList;
use Code16\Sharp\Utils\Entities\SharpEntityManager;

class EntityListInstanceStateControllerTest extends BaseApiTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    /** @test */
    public function we_can_update_the_state_of_an_entity_from_a_list()
    {
        $this->buildTheWorld();
        $this->withoutExceptionHandling();

        $this
            ->json('post', '/sharp/api/list/person/state/1', [
                'attribute' => 'state',
                'value' => 'ok',
            ])
            ->assertStatus(200)
            ->assertJson([
                'action' => 'refresh',
                'value' => 'ok',
            ]);
    }

    /** @test */
    public function we_can_return_a_reload_action_on_state_update()
    {
        $this->buildTheWorld();

        $this
            ->json('post', '/sharp/api/list/person/state/1', [
                'attribute' => 'state',
                'value' => 'ok_reload',
            ])
            ->assertStatus(200)
            ->assertJson([
                'action' => 'reload',
                'value' => 'ok_reload',
            ]);
    }

    /** @test */
    public function we_can_return_a_refresh_listed_items_action_on_state_update()
    {
        $this->buildTheWorld();

        $this->json('post', '/sharp/api/list/person/state/1', [
            'attribute' => 'state',
            'value' => 'ok_refresh_items',
        ])
            ->assertStatus(200)
            ->assertJson([
                'action' => 'refresh',
                'items' => [
                    [
                        'id' => 1,
                        'name' => 'John <b>Wayne</b>',
                        'age' => 22,
                    ],
                ],
                'value' => 'ok_refresh_items',
            ]);
    }

    /** @test */
    public function we_cant_update_the_state_of_an_entity_with_a_wrong_stateId()
    {
        $this->buildTheWorld();

        $this
            ->json('post', '/sharp/api/list/person/state/1', [
                'attribute' => 'state',
                'value' => 'invalid',
            ])
            ->assertStatus(422);
    }

    /** @test */
    public function applicative_exception_is_sent_back_as_417()
    {
        $this->buildTheWorld();

        $this
            ->json('post', '/sharp/api/list/person/state/1', [
                'attribute' => 'state',
                'value' => 'ko',
            ])
            ->assertStatus(417);
    }

    /** @test */
    public function we_cant_update_the_state_if_unauthorized()
    {
        $this->buildTheWorld();

        $this
            ->json('post', '/sharp/api/list/person/state/100', [
                'attribute' => 'state',
                'value' => 'anything',
            ])
            ->assertStatus(403);
    }

    protected function buildTheWorld($singleShow = false)
    {
        parent::buildTheWorld($singleShow);

        app(SharpEntityManager::class)
            ->entityFor('person')
            ->setList(EntityListInstanceStatePersonSharpEntityList::class);
    }
}

class EntityListInstanceStatePersonSharpEntityList extends PersonSharpEntityList
{
    public function buildListConfig(): void
    {
        $this->configureEntityState('state', new class() extends EntityState
        {
            protected function buildStates(): void
            {
                $this->addState('ok', 'OK', 'blue');
                $this->addState('ok_reload', 'OK', 'blue');
                $this->addState('ok_refresh_items', 'OK', 'blue');
                $this->addState('ko', 'KO2', 'red');
            }

            protected function updateState($instanceId, $stateId): array
            {
                if ($stateId == 'ko') {
                    throw new SharpApplicativeException('Nope');
                }

                if ($stateId == 'ok_reload') {
                    return $this->reload();
                }

                if ($stateId == 'ok_refresh_items') {
                    return $this->refresh($instanceId);
                }

                return [];
            }

            public function authorizeFor($instanceId): bool
            {
                return $instanceId != 100;
            }
        });
    }
}
