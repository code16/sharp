<?php

namespace Code16\Sharp\EntityList;

class EntityListEntities
{
    protected string $attribute;

    /**
     * @var EntityListEntity[]
     */
    protected array $entities = [];

    protected function __construct() {}

    public static function forAttribute(string $attribute): self
    {
        return new EntityListEntities()->setAttribute($attribute);
    }

    public function getAttribute(): string
    {
        return $this->attribute;
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

    public function setAttribute(string $attribute): self
    {
        $this->attribute = $attribute;

        return $this;
    }

    public function all(): array
    {
        return $this->entities;
    }
}
