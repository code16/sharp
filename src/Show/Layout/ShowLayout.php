<?php

namespace Code16\Sharp\Show\Layout;

use Code16\Sharp\Form\Layout\HasLayout;
use Illuminate\Support\Traits\Conditionable;

class ShowLayout implements HasLayout
{
    use Conditionable;

    protected array $sections = [];
    
    public function __construct(
        protected ShowLayoutLayoutFieldFactory $layoutFieldFactory,
    ) {
    }
    
    final public function addSection(string $label, \Closure $callback = null): self
    {
        $section = new ShowLayoutSection($label, $this->layoutFieldFactory);
        $this->sections[] = $section;

        if ($callback) {
            $callback($section);
        }

        return $this;
    }

    final public function addEntityListSection(string $entityListKey, ?bool $collapsable = null): self
    {
        $this->sections[] = (new ShowLayoutSection('', $this->layoutFieldFactory))
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
