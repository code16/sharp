<?php

namespace Code16\Sharp\Tests\Unit\EntityList;

use Code16\Sharp\EntityList\Containers\EntityListDataContainer;
use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\EntityState;
use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Tests\Unit\EntityList\Utils\SharpEntityDefaultTestList;

class SharpEntityListStateTest extends SharpTestCase
{
    /** @test */
    function we_can_get_list_entity_state_config_with_an_instance()
    {
        $list = new class extends SharpEntityDefaultTestList {
            function buildListConfig()
            {
                $this->setEntityState("_state", new class extends EntityState {
                    protected function buildStates()
                    {
                        $this->addState("test1", "Test 1", "blue");
                        $this->addState("test2", "Test 2", "red");
                    }
                    protected function updateState($instanceId, $stateId) {}
                });
            }
        };

        $this->assertArraySubset([
            "state" => [
                "attribute" => "_state",
                "values" => [
                    ["value"=>"test1", "label"=>"Test 1", "color"=>"blue"],
                    ["value"=>"test2", "label"=>"Test 2", "color"=>"red"],
                ]
            ]
        ], $list->listConfig());
    }

    /** @test */
    function we_can_get_list_entity_state_config_with_a_class_name()
    {
        $list = new class extends SharpEntityDefaultTestList {
            function buildListConfig()
            {
                $this->setEntityState("_state", SharpEntityListTestState::class);
            }
        };

        $this->assertArraySubset([
            "state" => [
                "attribute" => "_state",
                "values" => [
                    ["value"=>"test1", "label"=>"Test 1", "color"=>"blue"],
                    ["value"=>"test2", "label"=>"Test 2", "color"=>"red"],
                ]
            ]
        ], $list->listConfig());
    }

    /** @test */
    function entity_state_attribute_is_added_the_entity_data()
    {
        $form = new class extends SharpEntityDefaultTestList {
            function getListData(EntityListQueryParams $params): array
            {
                return [
                    ["name" => "John Wayne", "state" => true],
                    ["name" => "Mary Wayne", "state" => false]
                ];
            }
            function buildListDataContainers()
            {
                $this->addDataContainer(
                    EntityListDataContainer::make("name")
                );
            }
            function buildListConfig()
            {
                $this->setEntityState("state", new class extends EntityState {
                    protected function buildStates()
                    {
                        $this->addState(true, "Test 1", "blue");
                        $this->addState(false, "Test 2", "red");
                    }
                    protected function updateState($instanceId, $stateId) {}
                });
            }
        };

        $this->assertEquals([
            "items" => [
                ["name" => "John Wayne", "state" => true],
                ["name" => "Mary Wayne", "state" => false],
            ]
        ], $form->data());
    }
}

class SharpEntityListTestState extends EntityState
{
    protected function buildStates()
    {
        $this->addState("test1", "Test 1", "blue");
        $this->addState("test2", "Test 2", "red");
    }
    protected function updateState($instanceId, $stateId) {}
}