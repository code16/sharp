<?php

namespace Code16\Sharp\Utils\Menu;

use Closure;

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

    abstract public function build(): void;
}