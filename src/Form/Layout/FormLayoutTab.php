<?php

namespace Code16\Sharp\Form\Layout;

class FormLayoutTab
{
    /**
     * @var string
     */
    protected $label;

    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @param string $label
     */
    function __construct(string $label)
    {
        $this->label = $label;
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
     * @param FormLayoutColumn $column
     * @return FormLayoutColumn
     */
    private function addColumnLayout(FormLayoutColumn $column): FormLayoutColumn
    {
        $this->columns[] = $column;

        return $column;
    }
}