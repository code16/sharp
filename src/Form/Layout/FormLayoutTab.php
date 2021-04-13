<?php

namespace Code16\Sharp\Form\Layout;

class FormLayoutTab implements HasLayout
{
    protected string $title;
    protected array $columns = [];

    function __construct(string $title)
    {
        $this->title = $title;
    }

    public function addColumn(int $size, \Closure $callback = null): self
    {
        $column = $this->addColumnLayout(new FormLayoutColumn($size));

        if($callback) {
            $callback($column);
        }

        return $this;
    }

    public function toArray(): array
    {
        return [
            "title" => $this->title,
            "columns" => collect($this->columns)->map(function($column) {
                return $column->toArray();
            })->all()
        ];
    }

    public function addColumnLayout(FormLayoutColumn $column): FormLayoutColumn
    {
        $this->columns[] = $column;

        return $column;
    }
}