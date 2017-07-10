<?php

namespace Code16\Sharp\Tests\Unit\EntityList;

use Code16\Sharp\EntityList\Containers\EntityListDataContainer;
use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Tests\Unit\EntityList\Utils\SharpEntityDefaultTestList;
use Illuminate\Pagination\LengthAwarePaginator;

class SharpEntityListTest extends SharpTestCase
{
    /** @test */
    function we_can_get_containers()
    {
        $list = new class extends SharpEntityDefaultTestList {
            function buildListDataContainers()
            {
                $this->addDataContainer(
                    EntityListDataContainer::make("name")
                        ->setLabel("Name")
                );
            }
        };

        $this->assertEquals(["name" => [
            "key" => "name",
            "label" => "Name",
            "sortable" => false,
            "html" => true,
        ]], $list->dataContainers());
    }

    /** @test */
    function we_can_get_layout()
    {
        $list = new class extends SharpEntityDefaultTestList {
            function buildListDataContainers()
            {
                $this->addDataContainer(
                    EntityListDataContainer::make("name")
                )->addDataContainer(
                    EntityListDataContainer::make("age")
                );
            }
            function buildListLayout()
            {
                $this->addColumn("name", 6, 12)
                    ->addColumnLarge("age", 6);
            }
        };

        $this->assertEquals([
            [
                "key" => "name", "size" => 6, "sizeXS" => 12, "hideOnXS" => false,
            ], [
                "key" => "age", "size" => 6, "sizeXS" => 6, "hideOnXS" => true,
            ]
        ], $list->listLayout());
    }

    /** @test */
    function we_can_get_list_data()
    {
        $form = new class extends SharpEntityDefaultTestList {
            function getListData(EntityListQueryParams $params): array
            {
                return [
                    ["name" => "John Wayne", "age" => 22, "job" => "actor"],
                    ["name" => "Mary Wayne", "age" => 26, "job" => "truck driver"]
                ];
            }
            function buildListDataContainers()
            {
                $this->addDataContainer(
                    EntityListDataContainer::make("name")
                )->addDataContainer(
                    EntityListDataContainer::make("age")
                );
            }
        };

        $this->assertEquals([
            "items" => [
                ["name" => "John Wayne", "age" => 22],
                ["name" => "Mary Wayne", "age" => 26],
            ]
        ], $form->data());
    }

    /** @test */
    function we_can_get_paginated_list_data()
    {
        $form = new class extends SharpEntityDefaultTestList {
            function getListData(EntityListQueryParams $params)
            {
                $data = [
                    ["name" => "John Wayne", "age" => 22, "job" => "actor"],
                    ["name" => "Mary Wayne", "age" => 26, "job" => "truck driver"]
                ];

                return new LengthAwarePaginator($data, 10, 2, 1);
            }
            function buildListDataContainers()
            {
                $this->addDataContainer(
                    EntityListDataContainer::make("name")
                )->addDataContainer(
                    EntityListDataContainer::make("age")
                );
            }
        };

        $this->assertEquals([
            "items" => [
                ["name" => "John Wayne", "age" => 22],
                ["name" => "Mary Wayne", "age" => 26],
            ], "page" => 1, "pageSize" => 2, "totalCount" => 10
        ], $form->data());
    }

    /** @test */
    function we_can_get_list_config()
    {
        $list = new class extends SharpEntityDefaultTestList {
            function buildListConfig()
            {
                $this->setSearchable()
                    ->setPaginated();
            }
        };

        $this->assertEquals([
            "searchable" => true,
            "paginated" => true,
            "instanceIdAttribute" => "id",
            "displayMode" => "list",
            "defaultSort" => null,
            "defaultSortDir" => null
        ], $list->listConfig());
    }

//    /** @test */
//    function we_can_get_list_entity_command_config_with_an_instance()
//    {
//        $list = new class extends SharpEntityDefaultTestList {
//            function buildListConfig()
//            {
//                $this->addEntityCommand("_command", new class extends EntityCommand {
//                });
//            }
//        };
//
//        $this->assertArraySubset([
//            "commands" => [
//                [
//                    "key" => "_command",
//                    "label" => "My Command",
//                    "type" => "entity"
//                ]
//            ]
//        ], $list->listConfig());
//    }
}