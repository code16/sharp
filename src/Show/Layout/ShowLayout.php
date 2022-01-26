<?php

namespace Code16\Sharp\Show\Layout;

use Code16\Sharp\Form\Layout\HasLayout;

class ShowLayout implements HasLayout
{
    protected array $sections = [];

    final public function addSection(string $label, \Closure $callback = null): self
    {
        $section = new ShowLayoutSection($label);
        $this->sections[] = $section;

        if ($callback) {
            $callback($section);
        }

        return $this;
    }

    final public function addEntityListSection(string $entityListKey, \Closure $callback = null, bool $collapsable = false): self
    {
        $section = new ShowLayoutSection('');
        $section->setCollapsable($collapsable);
        $section->addColumn(12, function ($column) use ($entityListKey) {
            $column->withSingleField($entityListKey);
        });

        if ($callback) {
            $callback($section);
        }

        $this->sections[] = $section;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'sections' => collect($this->sections)
                ->map->toArray()
                ->all(),
        ];
    }
}
