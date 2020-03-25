<?php

namespace Code16\Sharp\Show\Layout;

use Code16\Sharp\Form\Layout\HasLayout;
use Code16\Sharp\Utils\Layout\LayoutField;

class ShowLayoutField extends LayoutField implements HasLayout
{
    
    /**
     * @inheritDoc
     */
    protected function getLayoutColumn()
    {
        return new ShowLayoutColumn(12);
    }
}