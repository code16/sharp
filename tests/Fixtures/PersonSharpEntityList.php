<?php

namespace Code16\Sharp\Tests\Fixtures;

use Code16\Sharp\EntityList\Commands\ReorderHandler;
use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\EntityList\Fields\EntityListFieldsLayout;
use Code16\Sharp\EntityList\Filters\EntityListSelectFilter;
use Code16\Sharp\EntityList\Filters\EntityListSelectMultipleFilter;
use Code16\Sharp\EntityList\Filters\EntityListSelectRequiredFilter;
use Code16\Sharp\EntityList\SharpEntityList;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class PersonSharpEntityList extends SharpEntityList
{
    public function getListData(): array|Arrayable
    {
        $items = [
            ['id' => 1, 'name' => 'John <b>Wayne</b>', 'age' => 22, 'job' => 'actor'],
            ['id' => 2, 'name' => 'Mary <b>Wayne</b>', 'age' => 26, 'job' => 'truck driver'],
        ];

        if ($this->queryParams->hasSearch()) {
            $items = collect($items)
                ->filter(function ($item) {
                    return Str::contains(
                        strtolower($item['name']),
                        $this->queryParams->searchWords(false)
                    );
                })
                ->toArray();
        }

        if ($age = $this->queryParams->filterFor(PersonSharpEntityListAgeFilter::class)) {
            $items = collect($items)
                ->filter(function ($item) use ($age) {
                    return $item['age'] == $age;
                })
                ->toArray();
        } elseif (request()->has('default_age')) {
            $items = collect($items)
                ->filter(function ($item) {
                    return $item['age'] == $this->queryParams->filterFor(PersonSharpEntityListAgeRequiredFilter::class);
                })
                ->toArray();
        }

        if ($ages = $this->queryParams->filterFor(PersonSharpEntityListAgeMultipleFilter::class)) {
            $items = collect($items)
                ->filter(function ($item) use ($ages) {
                    return in_array($item['age'], (array) $ages);
                })
                ->toArray();
        }

        if (count($this->queryParams->specificIds())) {
            $items = collect($items)
                ->filter(function ($item) {
                    return in_array($item['id'], $this->queryParams->specificIds());
                })
                ->toArray();
        }

        if (request()->has('paginated')) {
            return $this->transform(new LengthAwarePaginator($items, 20, 2, 1));
        }

        return $this->transform($items);
    }

    public function buildListFields(EntityListFieldsContainer $fieldsContainer): void
    {
        $fieldsContainer
            ->addField(
                EntityListField::make('name')
                    ->setLabel('Name')
                    ->setHtml()
                    ->setSortable()
            )
            ->addField(
                EntityListField::make('age')
                    ->setLabel('Age')
                    ->setSortable()
            );
    }

    public function buildListLayout(EntityListFieldsLayout $fieldsLayout): void
    {
        $fieldsLayout->addColumn('name', 6)
            ->addColumn('age', 6);
    }

    public function buildListLayoutForSmallScreens(EntityListFieldsLayout $fieldsLayout): void
    {
        $fieldsLayout->addColumn('name');
    }

    public function getFilters(): ?array
    {
        return [
            PersonSharpEntityListAgeFilter::class,
            PersonSharpEntityListAgeMultipleFilter::class,
            PersonSharpEntityListAgeRequiredFilter::class,
            PersonSharpEntityListAgeFilter::class,
        ];
    }

    public function buildListConfig(): void
    {
        $this->configureSearchable()
            ->configureReorderable(PersonSharpEntityListReorderHandler::class);
    }
}

class PersonSharpEntityListAgeFilter extends EntityListSelectFilter
{
    public function values(): array
    {
        return [22=>22, 23=>23, 24=>24, 25=>25, 26=>26];
    }
}

class PersonSharpEntityListAgeMultipleFilter extends EntityListSelectMultipleFilter
{
    public function values(): array
    {
        return [22=>22, 23=>23, 24=>24, 25=>25, 26=>26];
    }
}

class PersonSharpEntityListAgeRequiredFilter extends EntityListSelectRequiredFilter
{
    public function values(): array
    {
        return [22=>22, 23=>23, 24=>24, 25=>25, 26=>26];
    }

    public function defaultValue(): mixed
    {
        return 22;
    }
}

class PersonSharpEntityListReorderHandler implements ReorderHandler
{
    public function reorder(array $ids): void
    {
    }
}
