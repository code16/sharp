<?php

namespace Code16\Sharp\Form\Layout;

use Code16\Sharp\Utils\Layout\LayoutColumn;

class FormLayoutColumn extends LayoutColumn implements HasLayout
{
    /**
     * @var array
     */
    protected $fieldsets = [];

    /**
     * @param string $name
     * @param \Closure $callback
     * @return $this
     */
    public function withFieldset(string $name, \Closure $callback = null)
    {
        $fieldset = new FormLayoutFieldset($name);

        if($callback) {
            $callback($fieldset);
        }

        $this->addFieldsetLayout($fieldset);

        return $this;
    }

    /**
     * @param FormLayoutFieldset $fieldset
     */
    private function addFieldsetLayout(FormLayoutFieldset $fieldset)
    {
        $this->rows[] = [$fieldset];
    }
}