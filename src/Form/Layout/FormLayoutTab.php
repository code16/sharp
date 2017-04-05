<?php

namespace Code16\Sharp\Form\Layout;

class FormLayoutTab implements HasLayout
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
     * @return FormLayoutColumn
     */
    public function addColumn(int $size): FormLayoutColumn
    {
        return $this->addColumnLayout(new FormLayoutColumn($size));
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            "title" => $this->title,
            "columns" => collect($this->columns)->map(function($column) {
                return $column->toArray();
            })->all()
        ];
    }

    /**
     * @param FormLayoutColumn $column
     * @return FormLayoutColumn
     */
    private function addColumnLayout(FormLayoutColumn $column): FormLayoutColumn
    {
        $this->columns[] = $column;

        return $column;
    }
}