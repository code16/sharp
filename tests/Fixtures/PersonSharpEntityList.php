<?php

namespace Code16\Sharp\Tests\Fixtures;

use Code16\Sharp\EntityList\Commands\ReorderHandler;
use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\Filters\EntityListSelectFilter;
use Code16\Sharp\EntityList\Filters\EntityListSelectMultipleFilter;
use Code16\Sharp\EntityList\Filters\EntityListSelectRequiredFilter;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\EntityList\Fields\EntityListFieldsLayout;
use Code16\Sharp\EntityList\SharpEntityList;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class PersonSharpEntityList extends SharpEntityList
{
    function getListData(): array|Arrayable
    {
        $items = [
            ["id" => 1, "name" => "John <b>Wayne</b>", "age" => 22, "job" => "actor"],
            ["id" => 2, "name" => "Mary <b>Wayne</b>", "age" => 26, "job" => "truck driver"],
        ];

        if($this->queryParams->hasSearch()) {
            $items = collect($items)
                ->filter(function($item) {
                    return Str::contains(
                        strtolower($item["name"]), 
                        $this->queryParams->searchWords(false)
                    );
                })
                ->toArray();
        }

        if($this->queryParams->filterFor("age")) {
            $items = collect($items)
                ->filter(function($item) {
                    return $item["age"] == $this->queryParams->filterFor("age");
                })
                ->toArray();

        } elseif(request()->has("default_age")) {
            $items = collect($items)
                ->filter(function($item) {
                    return $item["age"] == $this->queryParams->filterFor("age_required");
                })
                ->toArray();
        }

        if($this->queryParams->filterFor("age_multiple")) {
            $items = collect($items)
                ->filter(function($item) {
                    return in_array($item["age"], (array)$this->queryParams->filterFor("age_multiple"));
                })
                ->toArray();
        }

        if(count($this->queryParams->specificIds())) {
            $items = collect($items)
                ->filter(function($item) {
                    return in_array($item["id"], $this->queryParams->specificIds());
                })
                ->toArray();
        }

        if(request()->has("paginated")) {
            return $this->transform(new LengthAwarePaginator($items, 20, 2, 1));
        }

        return $this->transform($items);
    }

    function buildListFields(EntityListFieldsContainer $fieldsContainer): void
    {
        $fieldsContainer
            ->addField(
                EntityListField::make("name")
                    ->setLabel("Name")
                    ->setHtml()
                    ->setSortable()
            )
            ->addField(
                EntityListField::make("age")
                    ->setLabel("Age")
                    ->setSortable()
            );
    }

    function buildListLayout(EntityListFieldsLayout $fieldsLayout): void
    {
        $fieldsLayout->addColumn("name", 6)
            ->addColumn("age", 6);
    }
    
    public function buildListLayoutForSmallScreens(EntityListFieldsLayout $fieldsLayout): void
    {
        $fieldsLayout->addColumn("name");
    }

    function buildListConfig(): void
    {
        $this->configureSearchable()
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

class PersonSharpEntityListAgeFilter implements EntityListSelectFilter
{
    public function values(): array
    {
        return [22=>22, 23=>23, 24=>24, 25=>25, 26=>26];
    }
}

class PersonSharpEntityListAgeMultipleFilter
    extends PersonSharpEntityListAgeFilter implements EntityListSelectMultipleFilter
{
}

class PersonSharpEntityListAgeRequiredFilter
    extends PersonSharpEntityListAgeFilter implements EntityListSelectRequiredFilter
{
    public function defaultValue()
    {
        return 22;
    }
}

class PersonSharpEntityListReorderHandler implements ReorderHandler
{
    function reorder(array $ids): void
    {
    }
}