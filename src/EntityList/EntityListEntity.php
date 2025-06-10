<?php

namespace Code16\Sharp\EntityList;

use Code16\Sharp\Utils\Entities\SharpEntity;
use Code16\Sharp\Utils\Entities\SharpEntityManager;

/**
 * @internal
 */
class EntityListEntity
{
    public function __construct(
        protected string $entityKeyOrClassName,
        protected ?string $icon = null,
        protected ?string $label = null
    ) {}

    public function getEntity(): SharpEntity
    {
        return app(SharpEntityManager::class)->entityFor($this->getEntityKey());
    }

    public function getEntityKey(): string
    {
        return app(SharpEntityManager::class)->entityKeyFor($this->entityKeyOrClassName);
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }
}
