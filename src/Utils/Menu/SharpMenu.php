<?php

namespace Code16\Sharp\Utils\Menu;

use Closure;
use Illuminate\Support\Collection;

abstract class SharpMenu
{
    use HasSharpMenuItems;

    protected ?SharpMenuUserMenu $userMenu = null;

    final public function addSection(string $title, Closure $callbackClosure): self
    {
        $section = new SharpMenuItemSection($title);
        $callbackClosure($section);
        $this->items[] = $section;

        return $this;
    }

    final public function setUserMenu(Closure $callbackClosure): self
    {
        $this->userMenu = new SharpMenuUserMenu();
        $callbackClosure($this->userMenu);

        return $this;
    }

    final public function items(): Collection
    {
        return collect($this->items);
    }

    final public function userMenu(): ?SharpMenuUserMenu
    {
        return $this->userMenu;
    }

    abstract public function build(): self;
}
