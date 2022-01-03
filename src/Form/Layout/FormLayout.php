<?php

namespace Code16\Sharp\Form\Layout;

class FormLayout implements HasLayout
{
    protected array $tabs = [];
    protected bool $tabbed = true;

    public final function addTab(string $label, \Closure $callback = null): self
    {
        $tab = $this->addTabLayout(new FormLayoutTab($label));

        if($callback) {
            $callback($tab);
        }

        return $this;
    }

    public final function addColumn(int $size, \Closure $callback = null): self
    {
        $column = $this
            ->getLonelyTab()
            ->addColumnLayout(
                new FormLayoutColumn($size)
            );

        if($callback) {
            $callback($column);
        }

        return $this;
    }

    public final function setTabbed(bool $tabbed = true): self
    {
        $this->tabbed = $tabbed;

        return $this;
    }

    private function addTabLayout(FormLayoutTab $tab): FormLayoutTab
    {
        $this->tabs[] = $tab;

        return $tab;
    }

    private function getLonelyTab(): FormLayoutTab
    {
        if(!sizeof($this->tabs)) {
            $this->addTabLayout(new FormLayoutTab("one"));
        }

        return $this->tabs[0];
    }

    function toArray(): array
    {
        return [
            "tabbed" => $this->tabbed,
            "tabs" => collect($this->tabs)
                ->map->toArray()
                ->all()
        ];
    }
}