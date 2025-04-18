<?php

namespace Code16\Sharp\Show\Layout;

use Code16\Sharp\Form\Layout\HasLayout;
use Code16\Sharp\Utils\Layout\LayoutColumn;
use Code16\Sharp\Utils\Layout\LayoutField;

class ShowLayoutColumn extends LayoutColumn implements HasLayout
{
    protected function newLayoutField(string|array $fieldKey, ?\Closure $subLayoutCallback = null): LayoutField
    {
        return new ShowLayoutField($fieldKey, $subLayoutCallback);
    }
}
