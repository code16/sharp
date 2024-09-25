<?php

namespace Code16\Sharp\Http\Context;

use Code16\Sharp\Http\Context\Util\BreadcrumbItem;

/**
 * @deprecated use sharp()->context() instead
 */
class CurrentSharpRequest
{
    public function getCurrentBreadcrumbItem(): ?BreadcrumbItem
    {
        return sharp()->context()->breadcrumb()->currentSegment();
    }

    public function getPreviousShowFromBreadcrumbItems(?string $entityKey = null): ?BreadcrumbItem
    {
        return sharp()->context()->breadcrumb()->previousShowSegment($entityKey);
    }

    public function isEntityList(): bool
    {
        return sharp()->context()->isEntityList();
    }

    public function isShow(): bool
    {
        return sharp()->context()->isShow();
    }

    public function isForm(): bool
    {
        return sharp()->context()->isForm();
    }

    public function isCreation(): bool
    {
        return sharp()->context()->isCreation();
    }

    public function isUpdate(): bool
    {
        return sharp()->context()->isUpdate();
    }

    public function entityKey(): ?string
    {
        return sharp()->context()->entityKey();
    }

    public function instanceId(): ?string
    {
        return sharp()->context()->instanceId();
    }

    final public function globalFilterFor(string $handlerClassOrKey): array|string|null
    {
        return sharp()->context()->globalFilterValue($handlerClassOrKey);
    }
}
