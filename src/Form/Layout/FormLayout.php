<?php

namespace Code16\Sharp\Form\Layout;

class FormLayout implements HasLayout
{
    protected array $tabs = [];
    protected bool $tabbed = true;

    final public function addTab(string $label, \Closure $callback = null): self
    {
        $tab = $this->addTabLayout(new FormLayoutTab($label));

        if ($callback) {
            $callback($tab);
        }

        return $this;
    }

    final public function addColumn(int $size, \Closure $callback = null): self
    {
        $column = $this
            ->getLonelyTab()
            ->addColumnLayout(
                new FormLayoutColumn($size)
            );

        if ($callback) {
            $callback($column);
        }

        return $this;
    }

    final public function setTabbed(bool $tabbed = true): self
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
        if (!sizeof($this->tabs)) {
            $this->addTabLayout(new FormLayoutTab('one'));
        }

        return $this->tabs[0];
    }

    public function toArray(): array
    {
        return [
            'tabbed' => $this->tabbed,
            'tabs'   => collect($this->tabs)
                ->map->toArray()
                ->all(),
        ];
    }
}
