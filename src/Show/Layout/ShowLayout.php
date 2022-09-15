<?php

namespace Code16\Sharp\Show\Layout;

use Closure;
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

    final public function addEntityListSection(string $entityListKey, Closure|bool|null $collapsable = null): self
    {
        $section = new ShowLayoutSection('');
        $section->addColumn(12, function ($column) use ($entityListKey) {
            $column->withSingleField($entityListKey);
        });

        if ($collapsable !== null) {
            if (is_bool($collapsable)) {
                $section->setCollapsable($collapsable);
            } else {
                // This is a Closure, web handle this for legacy purpose
                $collapsable($section);
            }
        }

        $this->sections[] = $section;

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
