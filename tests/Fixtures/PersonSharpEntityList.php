<?php

namespace Code16\Sharp\Tests\Fixtures;

use Code16\Sharp\EntityList\Commands\ReorderHandler;
use Code16\Sharp\EntityList\Containers\EntityListDataContainer;
use Code16\Sharp\EntityList\EntityListFilter;
use Code16\Sharp\EntityList\EntityListMultipleFilter;
use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\EntityListRequiredFilter;
use Code16\Sharp\EntityList\SharpEntityList;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

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
                return Str::contains(strtolower($item["name"]), $params->searchWords(false));
            })->all();
        }

        if($params->filterFor("age")) {
            $items = collect($items)->filter(function($item) use($params) {
                return $item["age"] == $params->filterFor("age");
            })->all();

        } elseif(request()->has("default_age")) {
            $items = collect($items)->filter(function($item) use($params) {
                return $item["age"] == $params->filterFor("age_required");
            })->all();
        }

        if($params->filterFor("age_multiple")) {
            $items = collect($items)->filter(function($item) use($params) {
                return in_array($item["age"], (array)$params->filterFor("age_multiple"));
            })->all();
        }

        if(count($params->specificIds())) {
            $items = collect($items)->filter(function($item) use($params) {
                return in_array($item["id"], $params->specificIds());
            })->all();
        }

        if(request()->has("paginated")) {
            return $this->transform(new LengthAwarePaginator($items, 20, 2, 1));
        }

        return $this->transform($items);
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
        $this->setSearchable()
            ->setReorderable(PersonSharpEntityListReorderHandler::class)
            ->addFilter("age", PersonSharpEntityListAgeFilter::class, function($value) {
                session(["filter_age_was_set" => $value]);
            })
            ->addFilter("age_multiple", PersonSharpEntityListAgeMultipleFilter::class)
            ->addFilter("age_required", PersonSharpEntityListAgeRequiredFilter::class)
            ->addFilter("age_forced", PersonSharpEntityListAgeFilter::class, function($value, $params) {
                $params->forceFilterValue("age", $value);
            });
    }
}

class PersonSharpEntityListAgeFilter implements EntityListFilter
{
    /**
     * @return array
     */
    public function values()
    {
        return [22=>22, 23=>23, 24=>24, 25=>25, 26=>26];
    }
}

class PersonSharpEntityListAgeMultipleFilter
    extends PersonSharpEntityListAgeFilter implements EntityListMultipleFilter
{
}

class PersonSharpEntityListAgeRequiredFilter
    extends PersonSharpEntityListAgeFilter implements EntityListRequiredFilter
{
    /**
     * @return string|int
     */
    public function defaultValue()
    {
        return 22;
    }
}

class PersonSharpEntityListReorderHandler implements ReorderHandler
{
    function reorder(array $ids)
    {
    }
}