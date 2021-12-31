<?php

namespace Code16\Sharp\Utils\Menu;

use Code16\Sharp\Utils\Entities\SharpDashboardEntity;
use Code16\Sharp\Utils\Entities\SharpEntity;
use Code16\Sharp\Utils\Entities\SharpEntityManager;

class SharpMenuItem
{
    protected SharpEntity|SharpDashboardEntity|null $entity;
    
    public function __construct(protected ?string $entityKey, protected ?string $label, protected ?string $icon)
    {
        $this->entity = $this->entityKey
            ? app(SharpEntityManager::class)->entityFor($this->entityKey)
            : null;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function isSection(): bool
    {
        return false;
    }

    public function isDashboardEntity(): bool
    {
        return $this->isEntity() && $this->entity->isDashboard();
    }

    public function isEntity(): bool
    {
        return true;
    }

    public function getKey(): string
    {
        return $this->entityKey;
    }

    public function getUrl(): string
    {
        if($this->entity->isDashboard()) {
            return route('code16.sharp.dashboard', $this->entityKey);
        }
        
        return $this->entity->isSingle()
            ? route('code16.sharp.single-show', ["entityKey" => $this->entityKey])
            : route('code16.sharp.list', $this->entityKey);
    }
}