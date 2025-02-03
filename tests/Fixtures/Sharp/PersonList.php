<?php

namespace Code16\Sharp\Tests\Fixtures\Sharp;

use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\EntityList\SharpEntityList;
use Illuminate\Contracts\Support\Arrayable;

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

    public function buildList(EntityListFieldsContainer $fields): void
    {
        $fields
            ->addField(
                EntityListField::make('name')
                    ->setLabel('Name')
                    ->setHtml()
                    ->setWidth(6)
                    ->setSortable(),
            );
    }

    public function buildListConfig(): void {}
}
