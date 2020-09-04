<?php

namespace Code16\Sharp\Form\Layout;

use Code16\Sharp\Utils\Layout\LayoutField;

/**
 * Represents one field layout.
 *
 * Class FormLayoutField
 * @package Code16\Sharp\Form\Layout
 */
class FormLayoutField extends LayoutField implements HasLayout
{

    /**
     * @inheritDoc
     */
    protected function getLayoutColumn()
    {
        return new FormLayoutColumn(12);
    }
}