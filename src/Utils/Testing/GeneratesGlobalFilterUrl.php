<?php

namespace Code16\Sharp\Utils\Testing;

use Code16\Sharp\Filters\GlobalFilters\GlobalFilters;
use Illuminate\Support\Facades\URL;

trait GeneratesGlobalFilterUrl
{
    private ?string $globalFilter = null;

    public function withSharpGlobalFilterValues(array|string $globalFilterValues): self
    {
        $this->globalFilter = collect((array) $globalFilterValues)
            ->implode(GlobalFilters::$valuesUrlSeparator);

        return $this;
    }

    private function setGlobalFilterUrlDefault(): void
    {
        URL::defaults(['globalFilter' => $this->globalFilter ?: GlobalFilters::$defaultKey]);
    }
}
