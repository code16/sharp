<?php

namespace App\Sharp\TestModels;

use App\Models\TestModel;
use App\Sharp\TestCommand;
use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\EntityList\SharpEntityList;
use Illuminate\Contracts\Support\Arrayable;

class TestModelList extends SharpEntityList
{
    protected function buildList(EntityListFieldsContainer $fields): void
    {
        $fields
            ->addField(
                EntityListField::make('text')
                    ->setLabel('Text'),
            );
    }

    public function buildListConfig(): void {}

    protected function getInstanceCommands(): ?array
    {
        return [];
    }

    protected function getEntityCommands(): ?array
    {
        return [
            TestCommand::class,
        ];
    }

    protected function getFilters(): array
    {
        return [];
    }

    public function getListData(): array|Arrayable
    {
        return $this
            ->transform(TestModel::paginate(20));
    }
}
