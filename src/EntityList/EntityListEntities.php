<?php

namespace Code16\Sharp\EntityList;

class EntityListEntities
{
    /**
     * @var EntityListEntity[]
     */
    protected array $entities = [];

    public function __construct() {}

    public static function make()
    {
        return new static();
    }

    /**
     * @param  string|class-string  $entityKeyOrClassName
     * @return void
     */
    public function addEntity(string $key, string $entityKeyOrClassName, ?string $icon = null, ?string $label = null): self
    {
        $this->entities[$key] = new EntityListEntity($entityKeyOrClassName, $icon, $label);

        return $this;
    }

    public function find(string $key): ?EntityListEntity
    {
        return $this->entities[$key] ?? null;
    }

    public function all(): array
    {
        return $this->entities;
    }
}
