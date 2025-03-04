<?php

namespace Code16\Sharp\Utils\Links;

use Code16\Sharp\Exceptions\SharpInvalidBreadcrumbItemException;
use Code16\Sharp\Http\Context\Util\BreadcrumbItem;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Illuminate\Support\Traits\Conditionable;

class BreadcrumbBuilder
{
    use Conditionable;

    protected array $breadcrumbParts = [];

    public function appendEntityList(string $entityClassNameOrKey): self
    {
        if (! empty($this->breadcrumbParts)) {
            throw new SharpInvalidBreadcrumbItemException('Entity list must be the first breadcrumb item');
        }

        $entityKey = $this->resolveEntityKey($entityClassNameOrKey);
        $this->breadcrumbParts[] = new BreadcrumbItem('s-list', $entityKey);

        return $this;
    }

    public function appendSingleShowPage(string $entityClassNameOrKey): self
    {
        if (! empty($this->breadcrumbParts)) {
            throw new SharpInvalidBreadcrumbItemException('Single show page must be the first breadcrumb item');
        }

        $entityKey = $this->resolveEntityKey($entityClassNameOrKey);
        $this->breadcrumbParts[] = new BreadcrumbItem('s-show', $entityKey);

        return $this;
    }

    public function appendShowPage(string $entityClassNameOrKey, string $instanceId): self
    {
        if (empty($this->breadcrumbParts)) {
            throw new SharpInvalidBreadcrumbItemException('Show page can not be the first breadcrumb item');
        }

        $entityKey = $this->resolveEntityKey($entityClassNameOrKey);
        $this->breadcrumbParts[] = (new BreadcrumbItem('s-show', $entityKey))->setInstance($instanceId);

        return $this;
    }

    public function generateUri(): string
    {
        return collect($this->breadcrumbParts)
            ->map(fn (BreadcrumbItem $item) => $item->toUri())
            ->implode('/');
    }

    private function resolveEntityKey(string $entityClassNameOrKey): string
    {
        return class_exists($entityClassNameOrKey)
            ? app(SharpEntityManager::class)->entityKeyFor($entityClassNameOrKey)
            : $entityClassNameOrKey;
    }
}
