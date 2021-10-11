<?php

namespace Code16\Sharp\EntityList\Fields;

use Illuminate\Support\Collection;

class EntityListFieldsLayout
{
    protected array $columns = [];
    
    public final function addColumn(string $key, int $size = null): self
    {
        $this->columns[$key] = ($size ?: "fill");

        return $this;
    }

    public final function getSizeOf(string $key): int|string|null
    {
        return $this->columns[$key] ?? null;
    }

    public final function hasColumns(): bool
    {
        return sizeof($this->columns) > 0;
    }

    public final function getColumns(): Collection
    {
        return collect($this->columns);
    }
}