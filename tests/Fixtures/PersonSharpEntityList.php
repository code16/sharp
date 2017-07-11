<?php

namespace Code16\Sharp\Tests\Fixtures;

use Code16\Sharp\EntityList\Containers\EntityListDataContainer;
use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\SharpEntityList;
use Illuminate\Pagination\LengthAwarePaginator;

class PersonSharpEntityList extends SharpEntityList
{

    /**
     * Retrieve all rows data as array.
     *
     * @param EntityListQueryParams $params
     * @return array|LengthAwarePaginator
     */
    function getListData(EntityListQueryParams $params)
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

        if(count($params->specificIds())) {
            $items = collect($items)->filter(function($item) use($params) {
                return in_array($item["id"], $params->specificIds());
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
            EntityListDataContainer::make("name")
                ->setLabel("Name")
                ->setHtml()
                ->setSortable()

        )->addDataContainer(
            EntityListDataContainer::make("age")
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