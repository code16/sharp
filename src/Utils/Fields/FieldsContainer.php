<?php

namespace Code16\Sharp\Utils\Fields;

use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Show\Fields\SharpShowField;

class FieldsContainer
{
    protected array $fields = [];

    public function addField(SharpFormField|SharpShowField $field): self
    {
        $this->fields[] = $field;

        return $this;
    }

    public function getFields(): array
    {
        return $this->fields;
    }
}
