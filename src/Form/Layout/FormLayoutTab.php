<?php

namespace Code16\Sharp\Form\Layout;

class FormLayoutTab implements HasLayout
{
    protected string $title;
    protected array $columns = [];

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    /**
     * @param  (\Closure(FormLayoutColumn): mixed)|null  $callback
     * @return $this
     */
    public function addColumn(int $size, ?\Closure $callback = null): self
    {
        $column = $this->addColumnLayout(new FormLayoutColumn($size));

        if ($callback) {
            $callback($column);
        }

        return $this;
    }

    public function getColumn(int $index): FormLayoutColumn
    {
        return $this->columns[$index];
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'columns' => collect($this->columns)
                ->map(fn (FormLayoutColumn $column) => $column->toArray())
                ->all(),
        ];
    }

    public function addColumnLayout(FormLayoutColumn $column): FormLayoutColumn
    {
        $this->columns[] = $column;

        return $column;
    }
}
