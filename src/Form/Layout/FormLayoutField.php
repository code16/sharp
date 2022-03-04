<?php

namespace Code16\Sharp\Form\Layout;

use Code16\Sharp\Utils\Layout\LayoutField;

/**
 * Represents one field layout.
 *
 * Class FormLayoutField
 */
class FormLayoutField extends LayoutField implements HasLayout
{
    protected function getLayoutColumn(): FormLayoutColumn
    {
        return new FormLayoutColumn(12);
    }
}
