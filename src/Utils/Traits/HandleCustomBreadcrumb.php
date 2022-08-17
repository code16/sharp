<?php

namespace Code16\Sharp\Utils\Traits;

trait HandleCustomBreadcrumb
{
    protected ?string $breadcrumbAttribute = null;

    public function configureBreadcrumbCustomLabelAttribute(string $breadcrumbAttribute): self
    {
        $this->breadcrumbAttribute = $breadcrumbAttribute;

        return $this;
    }

    protected function appendBreadcrumbCustomLabelAttribute(array &$config): void
    {
        if ($this->breadcrumbAttribute) {
            $config['breadcrumbAttribute'] = $this->breadcrumbAttribute;
        }
    }
}
