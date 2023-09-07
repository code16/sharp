<?php


use Code16\Sharp\EntityList\Commands\EntityState;
use Code16\Sharp\Exceptions\Form\SharpApplicativeException;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonList;
use Illuminate\Contracts\Support\Arrayable;

beforeEach(function () {
    login();

    config()->set(
        'sharp.entities.person',
        PersonEntity::class,
    );
});

it('updates the state of an instance from a list and return a "refresh" action by default', function () {
    fakeListFor('person', new class extends PersonList {
        public function buildListConfig(): void
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

        public function getListData(): array|Arrayable
        {
            return [
                ['id' => 1, 'name' => 'Marie Curie'],
                ['id' => 2, 'name' => 'Niels Bohr'],
            ];
        }
    });

    $this
        ->postJson(
            route('code16.sharp.api.list.state', ['person', 1]),
            [
                'attribute' => 'state',
                'value' => 'ok',
            ]
        )
        ->assertOk()
        ->assertJson([
            'action' => 'refresh',
            'items' => [
                ['id' => 1, 'name' => 'Marie Curie'],
                ['id' => 2, 'name' => 'Niels Bohr'],
            ],
            'value' => 'ok',
        ]);
});

it('allow to return a "reload" action', function () {
    fakeListFor('person', new class extends PersonList {
        public function buildListConfig(): void
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
                    return $this->reload();
                }
            });
        }
    });

    $this
        ->postJson(
            route('code16.sharp.api.list.state', ['person', 1]),
            [
                'attribute' => 'state',
                'value' => 'ok',
            ]
        )
        ->assertOk()
        ->assertJson([
            'action' => 'reload',
            'value' => 'ok',
        ]);
});

it('disallows to update the state of an entity with a wrong state', function () {
    fakeListFor('person', new class extends PersonList {
        public function buildListConfig(): void
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
            route('code16.sharp.api.list.state', ['person', 1]),
            [
                'attribute' => 'state',
                'value' => 'invalid',
            ]
        )
        ->assertStatus(422);
});

it('returns a 417 on an applicative exception', function () {
    fakeListFor('person', new class extends PersonList {
        public function buildListConfig(): void
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
                    throw new SharpApplicativeException('Nope');
                }
            });
        }
    });

    $this
        ->postJson(
            route('code16.sharp.api.list.state', ['person', 1]),
            [
                'attribute' => 'state',
                'value' => 'ok',
            ]
        )
        ->assertStatus(417);
});

it('disallows to update the state if unauthorized', function () {
    fakeListFor('person', new class extends PersonList {
        public function buildListConfig(): void
        {
            $this->configureEntityState('state', new class extends EntityState
            {
                protected function buildStates(): void
                {
                }
                protected function updateState($instanceId, string $stateId): ?array
                {
                    return null;
                }
                public function authorizeFor(mixed $instanceId): bool
                {
                    return false;
                }
            });
        }
    });

    $this
        ->postJson(
            route('code16.sharp.api.list.state', ['person', 1]),
            [
                'attribute' => 'state',
                'value' => 'ok',
            ]
        )
        ->assertStatus(403);
});
