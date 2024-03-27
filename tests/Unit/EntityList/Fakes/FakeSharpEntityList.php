<?php

namespace Code16\Sharp\Tests\Unit\EntityList\Fakes;

use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\EntityList\SharpEntityList;
use Illuminate\Contracts\Support\Arrayable;

class FakeSharpEntityList extends SharpEntityList
{
    public function buildList(EntityListFieldsContainer $fields): void
    {
    }

    public function buildListConfig(): void
    {
    }

    public function getListData(): array|Arrayable
    {
        return [];
    }
}
