<?php

use Code16\Sharp\EntityList\Commands\EntityState;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Entities\SinglePersonEntity;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonShow;
use Code16\Sharp\Tests\Fixtures\Sharp\SinglePersonShow;

beforeEach(function () {
    login();
});

it('updates the state of an instance from a show and return a "refresh" action by default', function () {
    sharpConfig()->addEntity('person', PersonEntity::class);

    fakeShowFor('person', new class extends PersonShow
    {
        public function buildShowConfig(): void
        {
            $this->configureEntityState('state', new class extends EntityState
            {
                protected function buildStates(): void
                {
                    $this->addState('ok', 'Ok', 'green')
                        ->addState('ko', 'KO', 'red');
                }

                protected function updateState($instanceId, string $stateId): ?array
                {
                    return null;
                }
            });
        }
    });

    $this
        ->postJson(
            route('code16.sharp.api.show.state', ['person', 1]),
            [
                'attribute' => 'state',
                'value' => 'ok',
            ]
        )
        ->assertOk()
        ->assertJson([
            'action' => 'refresh',
            'value' => 'ok',
        ]);
});

it('allows to update the state of an instance from a single show', function () {
    sharpConfig()->addEntity('person', SinglePersonEntity::class);

    fakeShowFor('person', new class extends SinglePersonShow
    {
        public function buildShowConfig(): void
        {
            $this->configureEntityState('state', new class extends EntityState
            {
                protected function buildStates(): void
                {
                    $this->addState('ok', 'Ok', 'green')
                        ->addState('ko', 'KO', 'red');
                }

                protected function updateState($instanceId, string $stateId): ?array
                {
                    return null;
                }
            });
        }
    });

    $this
        ->postJson(
            route('code16.sharp.api.show.state', ['person']),
            [
                'attribute' => 'state',
                'value' => 'ok',
            ]
        )
        ->assertOk()
        ->assertJson([
            'action' => 'refresh',
            'items' => [],
            'value' => 'ok',
        ]);
});
