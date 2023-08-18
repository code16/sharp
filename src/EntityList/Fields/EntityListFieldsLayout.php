<?php

namespace Code16\Sharp\EntityList\Fields;

use Illuminate\Support\Collection;

/**
 * @deprecated this class will be removed in next major version
 */
class EntityListFieldsLayout
{
    protected array $columns = [];

    final public function addColumn(string $key, int $size = null): self
    {
        $this->columns[$key] = ($size ?: 'fill');

        return $this;
    }

    final public function getSizeOf(string $key): int|string|null
    {
        return $this->columns[$key] ?? null;
    }

    final public function hasColumns(): bool
    {
        return sizeof($this->columns) > 0;
    }

    final public function getColumns(): Collection
    {
        return collect($this->columns);
    }
}
