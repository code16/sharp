<?php

namespace Code16\Sharp\Utils\Menu;

use Closure;
use Illuminate\Support\Collection;

abstract class SharpMenu
{
    use HasMenuEntityLinks;
    
    public final function addSection(string $title, Closure $callbackClosure): self
    {
        $section = new SharpMenuSection($title);
        $callbackClosure($section);
        $this->items[] = $section;
        return $this;
    }

    public final function items(): Collection
    {
        return collect($this->items);
    }

    abstract public function build(): self;
}