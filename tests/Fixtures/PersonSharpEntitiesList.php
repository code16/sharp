<?php

namespace Code16\Sharp\Tests\Fixtures;

use Code16\Sharp\EntitiesList\containers\EntitiesListDataContainer;
use Code16\Sharp\EntitiesList\EntitiesListQueryParams;
use Code16\Sharp\EntitiesList\SharpEntitiesList;

class PersonSharpEntitiesList extends SharpEntitiesList
{

    /**
     * Retrieve all rows data as array.
     *
     * @param EntitiesListQueryParams $params
     * @return array
     */
    function getListData(EntitiesListQueryParams $params)
    {
        return [
            ["id" => 1, "name" => "John <b>Wayne</b>", "age" => 22, "job" => "actor"],
            ["id" => 2, "name" => "Mary <b>Wayne</b>", "age" => 26, "job" => "truck driver"],
        ];
    }

    /**
     * Build list containers using ->addDataContainer()
     *
     * @return void
     */
    function buildListDataContainers()
    {
        $this->addDataContainer(
            EntitiesListDataContainer::make("name")
                ->setLabel("Name")
                ->setHtml()
                ->setSortable()

        )->addDataContainer(
            EntitiesListDataContainer::make("age")
                ->setLabel("Age")
                ->setSortable()
        );
    }

    /**
     * Build list layout using ->addColumn()
     *
     * @return void
     */
    function buildListLayout()
    {
        $this->addColumn("name", 6, 12)
            ->addColumnLarge("age", 6);
    }
}