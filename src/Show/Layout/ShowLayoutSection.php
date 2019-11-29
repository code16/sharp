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
    public function addColumn(int $size, \Closure $callback = null)
    {
        $column = $this->addColumnLayout(new ShowLayoutColumn($size));

        if($callback) {
            $callback($column);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            "title" => $this->title,
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