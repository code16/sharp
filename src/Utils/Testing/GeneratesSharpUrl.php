<?php

namespace Code16\Sharp\Utils\Testing;

use Closure;
use Code16\Sharp\Filters\GlobalFilters\GlobalFilters;
use Code16\Sharp\Utils\Links\BreadcrumbBuilder;
use Illuminate\Support\Facades\URL;

/**
 * @internal
 */
trait GeneratesSharpUrl
{
    private BreadcrumbBuilder $breadcrumbBuilder;
    private ?string $globalFilter = null;

    /**
     * @param  (\Closure(BreadcrumbBuilder): BreadcrumbBuilder)  $callback
     * @return $this
     */
    public function withSharpBreadcrumb(Closure $callback): self
    {
        $this->breadcrumbBuilder = $callback(new BreadcrumbBuilder());

        return $this;
    }

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

    private function breadcrumbBuilder(string $entityKey, ?string $instanceId = null): BreadcrumbBuilder
    {
        if (isset($this->breadcrumbBuilder)) {
            return $this->breadcrumbBuilder;
        }

        return (new BreadcrumbBuilder())
            ->appendEntityList($entityKey)
            ->when($instanceId, fn ($builder) => $builder->appendShowPage($entityKey, $instanceId));
    }

    private function buildCurrentPageUrl(BreadcrumbBuilder $builder): string
    {
        return url(
            sprintf(
                '/%s/%s/%s',
                sharp()->config()->get('custom_url_segment'),
                sharp()->context()->globalFilterUrlSegmentValue(),
                $builder->generateUri()
            )
        );
    }
}
