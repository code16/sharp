<?php

namespace Code16\Sharp\Show\Layout;

use Closure;
use Code16\Sharp\Form\Layout\HasLayout;
use Illuminate\Support\Traits\Conditionable;

class ShowLayout implements HasLayout
{
    use Conditionable;

    protected array $sections = [];

    /**
     * @param  string|(\Closure(ShowLayoutSection): mixed)  $label
     * @param  (\Closure(ShowLayoutSection): mixed)|null  $callback
     * @return $this
     */
    final public function addSection(string|Closure $label, ?Closure $callback = null): self
    {
        if ($label instanceof Closure) {
            $callback = $label;
            $label = '';
        }

        $section = new ShowLayoutSection($label);
        $this->sections[] = $section;

        if ($callback) {
            $callback($section);
        }

        return $this;
    }

    final public function addEntityListSection(string $entityListKey, ?bool $collapsable = null): self
    {
        $this->sections[] = (new ShowLayoutSection(''))
            ->addColumn(12, fn ($column) => $column->withField($entityListKey))
            ->when($collapsable !== null, fn ($section) => $section->setCollapsable($collapsable));

        return $this;
    }

    public function toArray(): array
    {
        return [
            'sections' => collect($this->sections)
                ->map(fn ($section) => $section->toArray())
                ->all(),
        ];
    }
}
