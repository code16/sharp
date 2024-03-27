<?php

namespace Code16\Sharp\Show\Fields\Formatters;

use Code16\Sharp\Show\Fields\SharpShowField;

class SharpShowFieldFormatter
{
    protected ?array $dataLocalizations = null;

    public function toFront(SharpShowField $field, $value)
    {
        return $value;
    }

    public function setDataLocalizations(array $dataLocalizations): static
    {
        $this->dataLocalizations = $dataLocalizations;

        return $this;
    }
}
