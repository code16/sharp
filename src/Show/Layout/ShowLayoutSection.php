<?php

namespace Code16\Sharp\Show\Layout;

use Code16\Sharp\Form\Layout\HasLayout;

class ShowLayoutSection implements HasLayout
{
    protected ?string $title = null;
    protected array $columns = [];
    protected bool $collapsable = false;

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    public function addColumn(int $size, \Closure $callback = null): self
    {
        $column = $this->addColumnLayout(new ShowLayoutColumn($size));

        if ($callback) {
            $callback($column);
        }

        return $this;
    }

    public function setCollapsable(bool $collapsable = true): self
    {
        $this->collapsable = $collapsable;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'collapsable' => $this->collapsable,
            'columns' => collect($this->columns)
                ->map(function ($column) {
                    return $column->toArray();
                })
                ->all(),
        ];
    }

    public function addColumnLayout(ShowLayoutColumn $column): ShowLayoutColumn
    {
        $this->columns[] = $column;

        return $column;
    }
}
