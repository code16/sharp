<?php

namespace Code16\Sharp\Form\Layout;

use Code16\Sharp\Utils\Layout\LayoutColumn;

class FormLayoutColumn extends LayoutColumn implements HasLayout
{
    protected array $fieldsets = [];

    public function withFieldset(string $name, \Closure $callback = null): self
    {
        $fieldset = new FormLayoutFieldset($name);

        if($callback) {
            $callback($fieldset);
        }

        $this->addFieldsetLayout($fieldset);

        return $this;
    }

    private function addFieldsetLayout(FormLayoutFieldset $fieldset)
    {
        $this->rows[] = [$fieldset];
    }
}