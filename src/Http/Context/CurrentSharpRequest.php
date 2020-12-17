<?php

namespace Code16\Sharp\Http\Context;

use Code16\Sharp\Http\Context\Util\BreadcrumbItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

class CurrentSharpRequest
{
    /** @var Collection */
    protected $breadcrumb = null;

    public function breadcrumb(): Collection
    {
        if($this->breadcrumb === null) {
            $this->buildBreadcrumb();
        }
        
        return $this->breadcrumb;
    }

    public function isShow(): bool
    {
        return $this->breadcrumb()->last()->isShow();
    }

    public function isForm(): bool
    {
        return $this->breadcrumb()->last()->isForm();
    }

    public function entityKey(): string
    {
        return $this->breadcrumb()->last()->entityKey();
    }

    public function instanceId(): ?string
    {
        return $this->breadcrumb()->last()->instanceId();
    }

    private function buildBreadcrumb(): void
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

    private function getSegmentsFromRequest(): Collection
    {
        if(request()->wantsJson()) {
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