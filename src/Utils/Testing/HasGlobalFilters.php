<?php

namespace Code16\Sharp\Utils\Testing;

use Code16\Sharp\Filters\GlobalFilters\GlobalFilters;
use Code16\Sharp\Filters\GlobalRequiredFilter;
use Illuminate\Support\Facades\URL;
use PHPUnit\Framework\Assert as PHPUnit;

/**
 * @internal
 */
trait HasGlobalFilters
{
    private array $globalFilterValues = [];
    private ?string $globalFilterSegment = null;

    public function withSharpGlobalFilter(string $globalFilterKeyOrClassName, mixed $value): static
    {
        $filter = app(GlobalFilters::class)->filterContainer()->findFilterHandler($globalFilterKeyOrClassName);

        if (! $filter) {
            $declaredFilter = app(GlobalFilters::class)->getDeclaredFilter($globalFilterKeyOrClassName);
            PHPUnit::assertNotNull($declaredFilter, "Global filter [$globalFilterKeyOrClassName] is not declared.");
            PHPUnit::assertTrue($declaredFilter->authorize(), "Global filter [$globalFilterKeyOrClassName] is not authorized.");
            PHPUnit::assertNotEmpty($declaredFilter->values(), "Global filter [$globalFilterKeyOrClassName] has no values.");
        }

        $this->globalFilterValues[$filter->getKey()] = $value;

        $this->setGlobalFilterUrlDefault();

        return $this;
    }

    /**
     * @deprecated use withSharpGlobalFilter() instead
     */
    public function withSharpGlobalFilterValues(array|string $globalFilterValues): static
    {
        $this->globalFilterSegment = collect((array) $globalFilterValues)
            ->implode(GlobalFilters::$valuesUrlSeparator);

        $this->setGlobalFilterUrlDefault();

        return $this;
    }

    /**
     * @internal
     */
    public function setGlobalFilterUrlDefault(): static
    {
        if ($this->globalFilterSegment) {
            URL::defaults(['globalFilter' => $this->globalFilterSegment]);
        } else {
            $values = collect(app(GlobalFilters::class)->getFilters())
                ->map(fn (GlobalRequiredFilter $globalFilter) => $this->globalFilterValues[$globalFilter->getKey()] ?? $globalFilter->defaultValue()
                )
                ->implode(GlobalFilters::$valuesUrlSeparator);

            URL::defaults(['globalFilter' => $values ?: GlobalFilters::$defaultKey]);
        }

        return $this;
    }
}
