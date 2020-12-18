<?php

namespace Code16\Sharp\Show\Layout;

use Code16\Sharp\Form\Layout\HasLayout;

class ShowLayoutSection implements HasLayout
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @var bool
     */
    protected $collapsable = false;

    /**
     * @param string $title
     */
    function __construct(string $title)
    {
        $this->title = $title;
    }

    /**
     * @param int $size
     * @param \Closure|null $callback
     * @return $this
     */
    public function addColumn(int $size, \Closure $callback = null): self
    {
        $column = $this->addColumnLayout(new ShowLayoutColumn($size));

        if($callback) {
            $callback($column);
        }

        return $this;
    }

    public function setCollapsable(bool $collapsable = true): self
    {
        $this->collapsable = $collapsable;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            "title" => $this->title,
            "collapsable" => $this->collapsable,
            "columns" => collect($this->columns)
                ->map(function($column) {
                    return $column->toArray();
                })
                ->all()
        ];
    }

    /**
     * @param ShowLayoutColumn $column
     * @return ShowLayoutColumn
     */
    public function addColumnLayout(ShowLayoutColumn $column): ShowLayoutColumn
    {
        $this->columns[] = $column;

        return $column;
    }
}
