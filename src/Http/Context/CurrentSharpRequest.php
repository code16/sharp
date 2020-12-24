<?php

namespace Code16\Sharp\Http\Context;

use Code16\Sharp\Http\Context\Util\BreadcrumbItem;
use Illuminate\Support\Collection;

class CurrentSharpRequest
{
    protected ?Collection $breadcrumb = null;

    public function breadcrumb(): Collection
    {
        if($this->breadcrumb === null) {
            $this->buildBreadcrumb();
        }
        
        return $this->breadcrumb;
    }

    public function getCurrentBreadcrumbItem(): ?BreadcrumbItem
    {
        return $this->breadcrumb()->last();
    }

    public function getPreviousShowFromBreadcrumbItems(?string $entityKey = null): ?BreadcrumbItem
    {
        return $this->breadcrumb()
            ->reverse()
            ->filter->isShow()
            ->when($entityKey !== null, function($items) use($entityKey) {
                return $items
                    ->filter(function(BreadcrumbItem $breadcrumbItem) use($entityKey) {
                        return $breadcrumbItem->entityKey() === $entityKey;
                    });
            })
            ->first();
    }

    public function isEntityList(): bool
    {
        $current = $this->getCurrentBreadcrumbItem();
        return $current ? $current->isEntityList() : false;
    }

    public function isShow(): bool
    {
        $current = $this->getCurrentBreadcrumbItem();
        return $current ? $current->isShow() : false;
    }

    public function isForm(): bool
    {
        $current = $this->getCurrentBreadcrumbItem();
        return $current ? $current->isForm() : false;
    }

    public function isCreation(): bool
    {
        $current = $this->getCurrentBreadcrumbItem();
        return $current 
            ? $current->isForm() && !$current->isSingleForm() && $current->instanceId() === null 
            : false;
    }

    public function isUpdate(): bool
    {
        $current = $this->getCurrentBreadcrumbItem();
        return $current
            ? $current->isForm() && ($current->isSingleForm() || $current->instanceId() !== null)
            : false;
    }

    public function entityKey(): ?string
    {
        $current = $this->getCurrentBreadcrumbItem();
        return $current ? $current->entityKey() : null;
    }

    public function instanceId(): ?string
    {
        $current = $this->getCurrentBreadcrumbItem();
        return $current ? $current->instanceId() : null;
    }

    /**
     * @param string $filterName
     * @return array|string|null
     */
    public function globalFilterFor(string $filterName)
    {
        if(!$handlerClass = config("sharp.global_filters.$filterName")) {
            return null;
        }

        if(session()->has("_sharp_retained_global_filter_$filterName")) {
            return session()->get("_sharp_retained_global_filter_$filterName");
        }

        return app($handlerClass)->defaultValue();
    }

    protected function buildBreadcrumb(): void
    {
        $this->breadcrumb = new Collection();
        $segments = $this->getSegmentsFromRequest();
        $depth = 0;
        
        if(count($segments) !== 0) {
            $this->breadcrumb->add(
                (new BreadcrumbItem($segments[0], $segments[1]))->setDepth($depth++)
            );

            $segments = $segments->slice(2)->values();

            while ($segments->count() > 0) {
                $type = $segments->shift();
                $key = $instance = null;
                $segments
                    ->takeWhile(function (string $segment) {
                        return !in_array($segment, ["s-show", "s-form"]);
                    })
                    ->values()
                    ->each(function(string $segment, $index) use(&$key, &$instance) {
                        if($index === 0) {
                            $key = $segment;
                        } elseif($index === 1) {
                            $instance = $segment;
                        }
                    });

                $segments = $segments->slice($instance !== null ? 2 : 1)->values();

                $this->breadcrumb->add(
                    (new BreadcrumbItem($type, $key))
                        ->setDepth($depth++)
                        ->setInstance($instance)
                );
            }
        }
    }

    protected function getSegmentsFromRequest(): Collection
    {
        if(request()->wantsJson() || request()->segment(2) === "api") {
            // API case: we use the referer
            $urlToParse = request()->header("referer");
            
            return collect(explode("/", parse_url($urlToParse)["path"]))
                ->filter(function(string $segment) {
                    return strlen(trim($segment)) && $segment !== sharp_base_url_segment();
                })
                ->values();
        }
        
        return collect(request()->segments())->slice(1)->values();
    }
}