<?php

use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\Tests\Unit\EntityList\Fakes\FakeEntityState;
use Code16\Sharp\Tests\Unit\EntityList\Fakes\FakeSharpEntityList;

it('gets list entity state config', function () {
    $list = new class extends FakeSharpEntityList
    {
        public function buildListConfig(): void
        {
            $this->configureEntityState('_state', new class extends FakeEntityState
            {
                protected function buildStates(): void
                {
                    $this->addState('test1', 'Test 1', 'blue');
                    $this->addState('test2', 'Test 2', 'red');
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
            $this->configureEntityState('state', new class extends FakeEntityState
            {
                protected function buildStates(): void
                {
                    $this->addState(true, 'Test 1', 'blue');
                    $this->addState(false, 'Test 2', 'red');
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

    expect($list->data()['items'])->toEqual([
        ['id' => 1, 'name' => 'Marie Curie', 'state' => true],
        ['id' => 2, 'name' => 'Albert Einstein', 'state' => false],
    ]);
});

it('handles authorization in a state', function () {
    $list = new class extends FakeSharpEntityList
    {
        public function buildListConfig(): void
        {
            $this->configureEntityState('_state', new class extends FakeEntityState
            {
                protected function buildStates(): void
                {
                    $this->addState(1, 'Test 1', 'blue');
                }

                public function authorizeFor($instanceId): bool
                {
                    return $instanceId < 3;
                }
            });
        }

        public function getListData(): array|\Illuminate\Contracts\Support\Arrayable
        {
            return [
                ['id' => 1], ['id' => 2], ['id' => 3],
                ['id' => 4], ['id' => 5], ['id' => 6],
            ];
        }
    };

    $list->buildListConfig();
    $list->data();

    expect($list->listConfig()['state'])
        ->toEqual([
            'attribute' => '_state',
            'values' => [
                ['value' => '1', 'label' => 'Test 1', 'color' => 'blue'],
            ],
            'authorization' => [1, 2],
        ]);
});

it('allows to add state field as a specific column', function () {
    $list = new class extends FakeSharpEntityList
    {
        public function buildListConfig(): void
        {
            $this->configureEntityState('my_state', new class extends FakeEntityState
            {
                protected function buildStates(): void
                {
                    $this->addState('test1', 'Test 1', 'blue')
                        ->addState('test2', 'Test 2', 'red');
                }
            });
        }
        
        public function buildList(EntityListFieldsContainer $fields): void
        {
            $fields
                ->addField(EntityListField::make('name'))
                ->addStateField(label: 'State')
                ->addField(EntityListField::make('age'));
        }
    };
    
    $list->buildListConfig();
    
    expect($list->fields())->toEqual([
        [
            'key' => 'name',
            'label' => '',
            'sortable' => false,
            'html' => true,
            'size' => 'fill',
            'hideOnXS' => false,
            'sizeXS' => 'fill'
        ],
        [
            'key' => '@state',
            'label' => 'State',
            'sortable' => false,
            'html' => false,
            'size' => 'fill',
            'hideOnXS' => false,
            'sizeXS' => 'fill'
        ],
        [
            'key' => 'age',
            'label' => '',
            'sortable' => false,
            'html' => true,
            'size' => 'fill',
            'hideOnXS' => false,
            'sizeXS' => 'fill'
        ]
    ]);
});

it('sends state field as last column if state configured and not declared as a column', function () {
    $list = new class extends FakeSharpEntityList
    {
        public function buildListConfig(): void
        {
            $this->configureEntityState('my_state', new class extends FakeEntityState
            {
                protected function buildStates(): void
                {
                    $this->addState('test1', 'Test 1', 'blue')
                        ->addState('test2', 'Test 2', 'red');
                }
            });
        }

        public function buildList(EntityListFieldsContainer $fields): void
        {
            $fields
                ->addField(EntityListField::make('name'))
                ->addField(EntityListField::make('age'))
                ->addField(EntityListField::make('picture'));
        }
    };
    
    $list->buildListConfig();

    expect($list->fields())->toHaveCount(4)
        ->and($list->fields()[3])->toEqual([
            'key' => '@state',
            'label' => '',
            'sortable' => false,
            'html' => false,
            'size' => 'fill',
            'hideOnXS' => false,
            'sizeXS' => 'fill'
        ]);
});

