<?php

namespace Code16\Sharp\Tests\Unit\EntityList;

use Code16\Sharp\EntityList\Commands\EntityState;
use Code16\Sharp\EntityList\Containers\EntityListDataContainer;
use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Tests\Unit\EntityList\Utils\SharpEntityDefaultTestList;

class SharpEntityListStateTest extends SharpTestCase
{
    /** @test */
    public function we_can_get_list_entity_state_config_with_an_instance()
    {
        $list = new class() extends SharpEntityDefaultTestList
        {
            public function buildListConfig(): void
            {
                $this->setEntityState('_state', new class() extends EntityState
                {
                    protected function buildStates(): void
                    {
                        $this->addState('test1', 'Test 1', 'blue');
                        $this->addState('test2', 'Test 2', 'red');
                    }

                    protected function updateState($instanceId, $stateId): array
                    {
                    }
                }, );
            }
        };

        $list->buildListConfig();

        $this->assertArraySubset([
            'state' => [
                'attribute' => '_state',
                'values' => [
                    ['value' => 'test1', 'label' => 'Test 1', 'color' => 'blue'],
                    ['value' => 'test2', 'label' => 'Test 2', 'color' => 'red'],
                ],
            ],
        ], $list->listConfig());
    }

    /** @test */
    public function we_can_get_list_entity_state_config_with_a_class_name()
    {
        $list = new class() extends SharpEntityDefaultTestList
        {
            public function buildListConfig(): void
            {
                $this->setEntityState('_state', SharpEntityListTestState::class);
            }
        };

        $list->buildListConfig();

        $this->assertArraySubset([
            'state' => [
                'attribute' => '_state',
                'values' => [
                    ['value' => 'test1', 'label' => 'Test 1', 'color' => 'blue'],
                    ['value' => 'test2', 'label' => 'Test 2', 'color' => 'red'],
                ],
            ],
        ], $list->listConfig());
    }

    /** @test */
    public function entity_state_attribute_is_added_the_entity_data()
    {
        $list = new class() extends SharpEntityDefaultTestList
        {
            public function getListData(EntityListQueryParams $params): array
            {
                return [
                    ['id' => 1, 'name' => 'John Wayne', 'state' => true],
                    ['id' => 2, 'name' => 'Mary Wayne', 'state' => false],
                ];
            }

            public function buildListDataContainers(): void
            {
                $this->addDataContainer(
                    EntityListDataContainer::make('name'),
                );
            }

            public function buildListConfig(): void
            {
                $this->setEntityState('state', new class() extends EntityState
                {
                    protected function buildStates(): void
                    {
                        $this->addState(true, 'Test 1', 'blue');
                        $this->addState(false, 'Test 2', 'red');
                    }

                    protected function updateState($instanceId, $stateId): array
                    {
                    }
                }, );
            }
        };

        $list->buildListConfig();

        $this->assertEquals([
            'items' => [
                ['id' => 1, 'name' => 'John Wayne', 'state' => true],
                ['id' => 2, 'name' => 'Mary Wayne', 'state' => false],
            ],
        ], $list->data());
    }

    /** @test */
    public function we_can_handle_authorization_in_a_state()
    {
        $list = new class() extends SharpEntityDefaultTestList
        {
            public function buildListConfig(): void
            {
                $this->setEntityState('_state', new class() extends EntityState
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
                }, );
            }
        };

        $list->buildListConfig();
        $list->data([
            ['id' => 1], ['id' => 2], ['id' => 3],
            ['id' => 4], ['id' => 5], ['id' => 6],
        ]);

        $this->assertArraySubset([
            'state' => [
                'attribute' => '_state',
                'values' => [
                    ['value' => '1', 'label' => 'Test 1', 'color' => 'blue'],
                ],
                'authorization' => [1, 2],
            ],
        ], $list->listConfig());
    }
}

class SharpEntityListTestState extends EntityState
{
    protected function buildStates(): void
    {
        $this->addState('test1', 'Test 1', 'blue');
        $this->addState('test2', 'Test 2', 'red');
    }

    protected function updateState($instanceId, $stateId): array
    {
    }
}
