<?php

namespace Code16\Sharp\Utils\Links;

use Code16\Sharp\Filters\GlobalFilters\GlobalFilters;
use Code16\Sharp\Utils\Entities\SharpEntityManager;

abstract class SharpLinkTo
{
    protected string $entityKey;
    protected string $tooltip = '';
    protected ?string $globalFilter = null;

    protected function __construct(string $entityClassOrKey)
    {
        $this->entityKey = app(SharpEntityManager::class)->entityKeyFor($entityClassOrKey);
    }

    public function setTooltip($tooltip): self
    {
        $this->tooltip = $tooltip;

        return $this;
    }

    public function withGlobalFilterValues(array|string $globalFilterValues): self
    {
        $this->globalFilter = collect((array) $globalFilterValues)
            ->implode(GlobalFilters::$valuesUrlSeparator);

        return $this;
    }

    public function renderAsText(string $text): string
    {
        return sprintf(
            '<a href="%s" title="%s">%s</a>',
            $this->renderAsUrl(),
            $this->tooltip,
            $text,
        );
    }

    abstract public function renderAsUrl(): string;
}
