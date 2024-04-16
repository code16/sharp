<?php

namespace Code16\Sharp\Utils\Fields;

use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Show\Fields\SharpShowField;
use Illuminate\Support\Traits\Conditionable;

class FieldsContainer
{
    use Conditionable;

    protected array $fields = [];

    public function addField(SharpFormField|SharpShowField $field): self
    {
        $this->fields[] = $field;

        return $this;
    }

    /**
     * @return (SharpFormField|SharpShowField)[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }
}
