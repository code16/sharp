<?php

namespace Code16\Sharp\Tests\Fixtures;

use Code16\Sharp\EntitiesList\Containers\EntitiesListDataContainer;
use Code16\Sharp\EntitiesList\EntitiesListQueryParams;
use Code16\Sharp\EntitiesList\SharpEntitiesList;
use Illuminate\Pagination\LengthAwarePaginator;

class PersonSharpEntitiesList extends SharpEntitiesList
{

    /**
     * Retrieve all rows data as array.
     *
     * @param EntitiesListQueryParams $params
     * @return array|LengthAwarePaginator
     */
    function getListData(EntitiesListQueryParams $params)
    {
        $items = [
            ["id" => 1, "name" => "John <b>Wayne</b>", "age" => 22, "job" => "actor"],
            ["id" => 2, "name" => "Mary <b>Wayne</b>", "age" => 26, "job" => "truck driver"],
        ];

        if($params->hasSearch()) {
            $items = collect($items)->filter(function($item) use($params) {
                return str_contains(strtolower($item["name"]), $params->searchWords(false));
            })->all();
        }

        if($params->filterFor("age")) {
            $items = collect($items)->filter(function($item) use($params) {
                return $item["age"] == $params->filterFor("age");
            })->all();
        }

        if(request()->has("paginated")) {
            return new LengthAwarePaginator($items, 20, 2, 1);
        }

        return $items;
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

    /**
     * Build list config
     *
     * @return void
     */
    function buildListConfig()
    {
        $this->setSearchable();
    }
}