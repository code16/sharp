<?php

namespace Code16\Sharp\Utils\Menu;

use Closure;
use Code16\Sharp\Auth\SharpAuthorizationManager;
use Code16\Sharp\Utils\Entities\SharpDashboardEntity;
use Code16\Sharp\Utils\Entities\SharpEntity;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\Links\SharpLinkTo;

class SharpMenuItemLink extends SharpMenuItem
{
    protected SharpEntity|SharpDashboardEntity|null $entity;
    protected ?string $entityKey = null;
    protected ?string $url = null;

    public function __construct(
        protected ?string $label,
        protected ?string $icon,
        protected ?Closure $badge,
        protected ?string $badgeTooltip = null,
        protected string|SharpLinkTo|null $badgeLink = null,
    ) {}

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
        return $this->label ?? $this->entityKey ?? '';
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function getBadge(): ?string
    {
        if ($this->badge instanceof Closure) {
            return ($this->badge)();
        }

        return null;
    }

    public function getBadgeTooltip(): ?string
    {
        return $this->badgeTooltip;
    }

    public function getBadgeUrl(): ?string
    {
        if ($this->badgeLink instanceof SharpLinkTo) {
            return $this->badgeLink->renderAsUrl();
        }

        return $this->badgeLink;
    }

    public function isDashboardEntity(): bool
    {
        return $this->isEntity() && $this->entity->isDashboard();
    }

    public function isEntity(): bool
    {
        return $this->entityKey !== null;
    }

    public function getEntityKey(): ?string
    {
        return $this->entityKey;
    }

    public function getUrl(): string
    {
        if ($this->isExternalLink()) {
            return $this->url;
        }

        if ($this->entity->isDashboard()) {
            return route('code16.sharp.dashboard', $this->entityKey);
        }

        return $this->entity->isSingle()
            ? route('code16.sharp.single-show', ['entityKey' => $this->entityKey])
            : route('code16.sharp.list', $this->entityKey);
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function isExternalLink(): bool
    {
        return ! $this->isEntity();
    }

    public function isAllowed(): bool
    {
        return $this->isExternalLink()
            || app(SharpAuthorizationManager::class)
                ->isAllowed('entity', $this->getEntityKey());
    }

    public function isCurrent(): bool
    {
        $rootEntityKey = sharp()->context()->breadcrumb()->breadcrumbItems()->first()?->entityKey();

        return $this->isEntity() && $rootEntityKey === $this->getEntityKey();
    }
}
