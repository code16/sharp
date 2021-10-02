<?php

namespace Code16\Sharp\EntityList\Layout;

class EntityListLayoutColumn
{
    protected string $key;
    protected int|string $size;
    protected int|string|null $sizeXS;

    public function __construct(string $key, int|string $size, int|string $sizeXS = null)
    {
        $this->key = $key;
        $this->size = $size;
        $this->sizeXS = $sizeXS;
    }

    public function toArray(): array
    {
        return [
            "key" => $this->key,
            "size" => $this->size,
            "sizeXS" => $this->sizeXS,
            "hideOnXS" => $this->sizeXS === null,
        ];
    }
}