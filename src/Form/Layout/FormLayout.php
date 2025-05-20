<?php

namespace Code16\Sharp\Form\Layout;

use Code16\Sharp\Exceptions\Form\SharpFormFieldLayoutException;
use Code16\Sharp\Form\Fields\SharpFormListField;
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

    private function addTabLayout(FormLayoutTab $tab): FormLayoutTab
    {
        $this->tabs[] = $tab;

        return $tab;
    }

    private function getLonelyTab(): FormLayoutTab
    {
        if (! count($this->tabs)) {
            $this->addTabLayout(new FormLayoutTab(''));
        }

        return $this->tabs[0];
    }

    public function validateAgainstFields(Collection $fields): self
    {
        collect($this->toArray()['tabs'])
            ->flatMap(fn ($tab) => collect($tab['columns'])
                ->flatMap(fn ($column) => collect($column['fields'])
                    ->flatMap(fn ($field) => $field)
                )
            )
            ->flatMap(fn ($field) => isset($field['legend'])
                ? collect($field['fields'])->flatMap(fn ($field) => $field)
                : [$field]
            )
            ->each(function ($layoutField) use ($fields) {
                if (! $fields->has($layoutField['key'])) {
                    throw SharpFormFieldLayoutException::undeclaredField($layoutField['key']);
                }

                $field = $fields->get($layoutField['key']);
                if (isset($layoutField['item']) && ! $field instanceof SharpFormListField) {
                    throw SharpFormFieldLayoutException::regularFieldDeclaredAsListField($layoutField['key']);
                }

                if (! isset($layoutField['item']) && $field instanceof SharpFormListField) {
                    throw SharpFormFieldLayoutException::listFieldDeclaredAsRegularField($layoutField['key']);
                }
            });

        return $this;
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
