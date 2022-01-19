<?php

namespace Code16\Sharp\View\Components\Content\Utils;

use Illuminate\Support\Collection;
use Illuminate\View\ComponentAttributeBag;

class ComponentAttributeBagCollection
{
    protected Collection $collection;

    public function __construct()
    {
        $this->collection = new Collection();
    }

    public function get(string $componentName): ?ComponentAttributeBag
    {
        return $this->collection->get($componentName);
    }

    public function put(string $componentName, ComponentAttributeBag|array $attributes): void
    {
        if (is_array($attributes)) {
            $attributes = new ComponentAttributeBag($attributes);
        }

        $this->collection[$componentName] = $this->mergedAttributes($componentName, $attributes);
    }

    protected function mergedAttributes(string $componentName, ComponentAttributeBag $attributes): ComponentAttributeBag
    {
        if ($existingAttributes = $this->get($componentName)) {
            return $attributes->merge($existingAttributes->getAttributes());
        }

        return $attributes;
    }
}
