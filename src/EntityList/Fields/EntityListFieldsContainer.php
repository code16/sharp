<?php

namespace Code16\Sharp\EntityList\Fields;

use Illuminate\Support\Collection;

class EntityListFieldsContainer
{
    protected array $fields = [];
    
    public final function addField(EntityListField $field): self
    {
        $this->fields[] = $field;

        return $this;
    }

    public final function getFields(): Collection
    {
        return collect($this->fields)
            ->map->toArray()
            ->keyBy("key");
    }
}