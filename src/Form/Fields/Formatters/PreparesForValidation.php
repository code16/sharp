<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;

interface PreparesForValidation
{
    public function prepareForValidation(SharpFormField $field, string $key, $value): array;
}
