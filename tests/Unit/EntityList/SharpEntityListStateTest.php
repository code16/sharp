<?php

use Code16\Sharp\EntityList\Commands\EntityState;
use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\Tests\Unit\EntityList\Fakes\FakeSharpEntityList;

it('gets list entity state config', function () {
    $list = new class extends FakeSharpEntityList
    {
        public function buildListConfig(): void
        {
            $this->configureEntityState('_state', new class extends EntityState
            {
                protected function buildStates(): void
                {
                    $this->addState('test1', 'Test 1', 'blue');
                    $this->addState('test2', 'Test 2', 'red');
                }

                protected function updateState($instanceId, $stateId): array
                {
                }
            });
        }
    };

    $list->buildListConfig();

    expect($list->listConfig()['state'])->toEqual([
        'attribute' => '_state',
        'values' => [
            ['value' => 'test1', 'label' => 'Test 1', 'color' => 'blue'],
            ['value' => 'test2', 'label' => 'Test 2', 'color' => 'red'],
        ],
        'authorization' => [],
    ]);
});

it('adds the entity state attribute to the entity data', function () {
    $list = new class extends FakeSharpEntityList
    {
        public function buildList($fields): void
        {
            $fields->addField(EntityListField::make('name'));
        }

        public function buildListConfig(): void
        {
            $this->configureEntityState('state', new class extends EntityState
            {
                protected function buildStates(): void
                {
                    $this->addState(true, 'Test 1', 'blue');
                    $this->addState(false, 'Test 2', 'red');
                }

                protected function updateState($instanceId, $stateId): array
                {
                }
            });
        }

        public function getListData(): array|\Illuminate\Contracts\Support\Arrayable
        {
            return [
                ['id' => 1, 'name' => 'Marie Curie', 'state' => true],
                ['id' => 2, 'name' => 'Albert Einstein', 'state' => false],
            ];
        }
    };

    $list->buildListConfig();

    expect($list->data()['list'])->toEqual([
        'items' => [
            ['id' => 1, 'name' => 'Marie Curie', 'state' => true],
            ['id' => 2, 'name' => 'Albert Einstein', 'state' => false],
        ],
    ]);
});

it('handles authorization in a state', function () {
    $list = new class extends FakeSharpEntityList
    {
        public function buildListConfig(): void
        {
            $this->configureEntityState('_state', new class extends EntityState
            {
                protected function buildStates(): void
                {
                    $this->addState(1, 'Test 1', 'blue');
                }

                public function authorizeFor($instanceId): bool
                {
                    return $instanceId < 3;
                }

                protected function updateState($instanceId, $stateId): array
                {
                }
            });
        }
    };

    $list->buildListConfig();
    $list->data([
        ['id' => 1], ['id' => 2], ['id' => 3],
        ['id' => 4], ['id' => 5], ['id' => 6],
    ]);

    expect($list->listConfig()['state'])
        ->toEqual([
            'attribute' => '_state',
            'values' => [
                ['value' => '1', 'label' => 'Test 1', 'color' => 'blue'],
            ],
            'authorization' => [1, 2],
        ]);
});