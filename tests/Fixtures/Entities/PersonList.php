<?php

namespace Code16\Sharp\Tests\Fixtures\Entities;

use Code16\Sharp\EntityList\Commands\ReorderHandler;
use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\EntityList\Filters\EntityListSelectFilter;
use Code16\Sharp\EntityList\Filters\EntityListSelectMultipleFilter;
use Code16\Sharp\EntityList\Filters\EntityListSelectRequiredFilter;
use Code16\Sharp\EntityList\SharpEntityList;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class PersonList extends SharpEntityList
{
    public function getListData(): array|Arrayable
    {
        $items = [
            ['id' => 1, 'name' => 'Marie Curie'],
            ['id' => 2, 'name' => 'Niels Bohr'],
        ];

        return $this->transform($items);
    }

    public function buildListFields(EntityListFieldsContainer $fieldsContainer): void
    {
        $fieldsContainer
            ->addField(
                EntityListField::make('name')
                    ->setLabel('Name')
                    ->setHtml()
                    ->setWidth(6)
                    ->setWidthOnSmallScreensFill()
                    ->setSortable(),
            );
    }

    public function buildListConfig(): void
    {
    }
}