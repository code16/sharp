<?php

namespace Code16\Sharp\Tests\Feature\Api\Commands;

use Code16\Sharp\EntityList\Commands\EntityState;
use Code16\Sharp\EntityList\Commands\SingleEntityState;
use Code16\Sharp\Tests\Feature\Api\BaseApiTest;
use Code16\Sharp\Tests\Fixtures\PersonSharpShow;
use Code16\Sharp\Tests\Fixtures\PersonSharpSingleShow;

class ShowInstanceStateControllerTest extends BaseApiTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    /** @test */
    public function we_can_update_the_state_of_an_entity_from_a_show()
    {
        $this->buildTheWorld();

        $this->postJson('/sharp/api/show/person/state/1', [
            'attribute' => 'state',
            'value' => 'ok',
        ])
            ->assertStatus(200)
            ->assertJson([
                'action' => 'refresh',
                'value' => 'ok',
            ]);

        $this->postJson('/sharp/api/show/person/state', [
            'attribute' => 'state',
            'value' => 'ok',
        ])
            ->assertStatus(404);
    }

    /** @test */
    public function we_can_update_the_state_of_an_entity_from_a_single_show()
    {
        $this->buildTheWorld(true);

        $this->postJson('/sharp/api/show/person/state', [
            'attribute' => 'state',
            'value' => 'ok',
        ])
            ->assertStatus(200)
            ->assertJson([
                'action' => 'reload',
                'value' => 'ok',
            ]);

        $this->postJson('/sharp/api/show/person/state/1', [
            'attribute' => 'state',
            'value' => 'ok',
        ])
            ->assertStatus(404);
    }

    protected function buildTheWorld($singleShow = false)
    {
        parent::buildTheWorld($singleShow);

        $this->app['config']->set(
            'sharp.entities.person.show',
            $singleShow
                ? ShowInstanceStatePersonSharpSingleShow::class
                : ShowInstanceStatePersonSharpShow::class,
        );
    }
}

class ShowInstanceStatePersonSharpShow extends PersonSharpShow
{
    public function buildShowConfig(): void
    {
        $this->configureEntityState('state', new class() extends EntityState
        {
            protected function buildStates(): void
            {
                $this->addState('ok', 'OK', 'blue');
                $this->addState('ko', 'KO2', 'red');
            }

            protected function updateState($instanceId, $stateId): array
            {
                if ($stateId == 'ok') {
                    return $this->refresh($instanceId);
                }
            }

            public function authorizeFor($instanceId): bool
            {
                return $instanceId != 100;
            }
        }, );
    }
}

class ShowInstanceStatePersonSharpSingleShow extends PersonSharpSingleShow
{
    public function buildShowConfig(): void
    {
        $this->setEntityState('state', new class() extends SingleEntityState
        {
            protected function buildStates(): void
            {
                $this->addState('ok', 'OK', 'blue');
                $this->addState('ko', 'KO2', 'red');
            }

            protected function updateSingleState(string $stateId): array
            {
                if ($stateId == 'ok') {
                    return $this->reload();
                }
            }

            public function authorize(): bool
            {
                return true;
            }
        }, );
    }
}
