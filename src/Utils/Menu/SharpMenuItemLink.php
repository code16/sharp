<?php

namespace Code16\Sharp\Utils\Menu;

use Code16\Sharp\Utils\Entities\SharpDashboardEntity;
use Code16\Sharp\Utils\Entities\SharpEntity;
use Code16\Sharp\Utils\Entities\SharpEntityManager;

class SharpMenuItemLink extends SharpMenuItem
{
    protected SharpEntity|SharpDashboardEntity|null $entity;
    protected ?string $entityKey = null;
    protected ?string $url = null;

    public function __construct(protected ?string $label, protected ?string $icon)
    {
    }

    public function setEntity(string $entityKey): self
    {
        $this->entityKey = $entityKey;
        $this->entity = $this->entityKey
            ? app(SharpEntityManager::class)->entityFor($this->entityKey)
            : null;
        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function isDashboardEntity(): bool
    {
        return $this->isEntity() && $this->entity->isDashboard();
    }

    public function isEntity(): bool
    {
        return $this->entityKey !== null;
    }

    public function getKey(): string
    {
        return $this->entityKey;
    }

    public function getUrl(): string
    {
        if($this->isExternalLink()) {
            return $this->url;
        }
        
        if($this->entity->isDashboard()) {
            return route('code16.sharp.dashboard', $this->entityKey);
        }
        
        return $this->entity->isSingle()
            ? route('code16.sharp.single-show', ["entityKey" => $this->entityKey])
            : route('code16.sharp.list', $this->entityKey);
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function isExternalLink(): bool
    {
        return !$this->isEntity();
    }
}