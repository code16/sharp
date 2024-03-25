<?php

namespace Code16\Sharp\Show\Fields\Formatters;

use Code16\Sharp\Show\Fields\SharpShowField;

class SharpShowFieldFormatter
{
    public function toFront(SharpShowField $field, $value)
    {
        return $value;
    }
}
