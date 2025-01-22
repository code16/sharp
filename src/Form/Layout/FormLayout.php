<?php

namespace Code16\Sharp\Form\Layout;

use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Conditionable;

class FormLayout implements HasLayout
{
    use Conditionable;

    protected array $tabs = [];
    protected bool $tabbed = true;

    /**
     * @param  (\Closure(FormLayoutTab): mixed)  $callback
     * @return $this
     */
    final public function addTab(string $label, ?\Closure $callback = null): self
    {
        $tab = $this->addTabLayout(new FormLayoutTab($label));

        if ($callback) {
            $callback($tab);
        }

        return $this;
    }

    final public function addColumn(int $size, ?\Closure $callback = null): self
    {
        $column = $this
            ->getLonelyTab()
            ->addColumnLayout(new FormLayoutColumn($size));

        if ($callback) {
            $callback($column);
        }

        return $this;
    }

    final public function getColumn(int $index): FormLayoutColumn
    {
        return $this
            ->getLonelyTab()
            ->getColumn($index);
    }

    final public function setTabbed(bool $tabbed = true): self
    {
        $this->tabbed = $tabbed;

        return $this;
    }
    
    /**
     * @internal
     */
    public function getAllColumns(): Collection
    {
        return collect($this->tabs)
            ->flatMap(fn (FormLayoutTab $tab) => $tab->getColumns());
    }

    private function addTabLayout(FormLayoutTab $tab): FormLayoutTab
    {
        $this->tabs[] = $tab;

        return $tab;
    }

    private function getLonelyTab(): FormLayoutTab
    {
        if (! count($this->tabs)) {
            $this->addTabLayout(new FormLayoutTab('one'));
        }

        return $this->tabs[0];
    }

    public function toArray(): array
    {
        return [
            'tabbed' => $this->tabbed,
            'tabs' => collect($this->tabs)
                ->map(fn (FormLayoutTab $tab) => $tab->toArray())
                ->all(),
        ];
    }
}
