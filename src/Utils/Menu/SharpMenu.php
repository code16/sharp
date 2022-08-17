<?php

namespace Code16\Sharp\Utils\Menu;

use Closure;
use Illuminate\Support\Collection;

abstract class SharpMenu
{
    use HasSharpMenuItems;

    final public function addSection(string $title, Closure $callbackClosure): self
    {
        $section = new SharpMenuItemSection($title);
        $callbackClosure($section);
        $this->items[] = $section;

        return $this;
    }

    final public function items(): Collection
    {
        return collect($this->items);
    }

    abstract public function build(): self;
}
