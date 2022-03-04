<?php

namespace Code16\Sharp\EntityList\Layout;

class EntityListLayoutColumn
{
    protected string $key;
    protected int $size;
    protected ?int $sizeXS;
    protected bool $largeOnly = false;

    public function __construct(string $key, int $size, int $sizeXS = null)
    {
        $this->key = $key;
        $this->size = $size;
        $this->sizeXS = $sizeXS ?: $size;
    }

    public function setLargeOnly(bool $largeOnly = true): void
    {
        $this->largeOnly = $largeOnly;
    }

    public function toArray(): array
    {
        return [
            'key'      => $this->key,
            'size'     => $this->size,
            'sizeXS'   => $this->sizeXS,
            'hideOnXS' => $this->largeOnly,
        ];
    }
}
