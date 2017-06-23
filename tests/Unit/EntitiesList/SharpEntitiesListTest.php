<?php

namespace Code16\Sharp\Tests\Unit\EntitiesList;

use Code16\Sharp\EntitiesList\containers\EntitiesListDataContainer;
use Code16\Sharp\EntitiesList\EntitiesListQueryParams;
use Code16\Sharp\EntitiesList\SharpEntitiesList;
use Code16\Sharp\Tests\SharpTestCase;

class SharpEntitiesListTest extends SharpTestCase
{
    /** @test */
    function we_can_get_containers()
    {
        $list = new class extends SharpEntitiesListTestList {
            function buildListDataContainers()
            {
                $this->addDataContainer(
                    EntitiesListDataContainer::make("name")
                        ->setLabel("Name")
                );
            }
        };

        $this->assertEquals(["name" => [
            "key" => "name",
            "label" => "Name",
            "sortable" => false,
            "html" => false,
        ]], $list->dataContainers());
    }

    /** @test */
    function we_can_get_layout()
    {
        $list = new class extends SharpEntitiesListTestList {
            function buildListDataContainers()
            {
                $this->addDataContainer(
                    EntitiesListDataContainer::make("name")
                )->addDataContainer(
                    EntitiesListDataContainer::make("age")
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
        $form = new class extends SharpEntitiesListTestList {
            function getListData(EntitiesListQueryParams $params): array
            {
                return [
                    ["name" => "John Wayne", "age" => 22, "job" => "actor"],
                    ["name" => "Mary Wayne", "age" => 26, "job" => "truck driver"]
                ];
            }
            function buildListDataContainers()
            {
                $this->addDataContainer(
                    EntitiesListDataContainer::make("name")
                )->addDataContainer(
                    EntitiesListDataContainer::make("age")
                );
            }
        };

        $this->assertEquals([
            ["name" => "John Wayne", "age" => 22],
            ["name" => "Mary Wayne", "age" => 26],
        ], $form->data(new EntitiesListQueryParams()));
    }
}

abstract class SharpEntitiesListTestList extends SharpEntitiesList
{
    function buildListDataContainers() {}
    function buildListLayout() {}
    function getListData(EntitiesListQueryParams $params) { return []; }
}