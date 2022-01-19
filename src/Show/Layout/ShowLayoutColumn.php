<?php

namespace Code16\Sharp\Show\Layout;

use Code16\Sharp\Form\Layout\HasLayout;
use Code16\Sharp\Utils\Layout\LayoutColumn;
use Code16\Sharp\Utils\Layout\LayoutField;

class ShowLayoutColumn extends LayoutColumn implements HasLayout
{
    /**
     * Override HasLayout::newLayoutField.
     *
     * @param  string  $fieldKey
     * @param  \Closure|null  $subLayoutCallback
     * @return \Code16\Sharp\Form\Layout\FormLayoutField|ShowLayoutField
     */
    protected function newLayoutField(string $fieldKey, \Closure $subLayoutCallback = null): LayoutField
    {
        return new ShowLayoutField($fieldKey, $subLayoutCallback);
    }
}
