<?php

namespace Code16\Sharp\Utils\Traits;

use Illuminate\Support\Str;

trait HandleCustomBreadcrumb
{
    protected ?string $breadcrumbAttribute = null;
    protected bool $breadcrumbLocalized = false;
    protected ?int $breadcrumbLimit = null;

    public function configureBreadcrumbCustomLabelAttribute(
        string $breadcrumbAttribute,
        ?int $limit = null,
        bool $localized = false,
    ): self {
        $this->breadcrumbAttribute = $breadcrumbAttribute;
        $this->breadcrumbLimit = $limit;
        $this->breadcrumbLocalized = $localized;

        return $this;
    }

    public function getBreadcrumbCustomLabel(array $data): ?string
    {
        if (! $this->breadcrumbAttribute) {
            return null;
        }

        $label = $this->breadcrumbLocalized
            ? $data[$this->breadcrumbAttribute][$this->getDataLocalizations()[0]] ?? null
            : $data[$this->breadcrumbAttribute] ?? null;

        return $label && $this->breadcrumbLimit
            ? Str::limit($label, $this->breadcrumbLimit)
            : $label;
    }
}
