<?php

namespace Code16\Sharp\Show\Fields\Formatters;

use Code16\Sharp\Show\Fields\SharpShowField;
use Code16\Sharp\Utils\Fields\Formatters\FormatsLocalizedValue;

class SharpShowFieldFormatter
{
    use FormatsLocalizedValue;

    public function toFront(SharpShowField $field, $value)
    {
        return $value;
    }
}
