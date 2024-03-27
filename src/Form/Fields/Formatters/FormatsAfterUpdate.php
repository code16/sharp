<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;

interface FormatsAfterUpdate
{
    public function afterUpdate(SharpFormField $field, string $attribute, mixed $value): mixed;
}
