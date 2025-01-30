<?php

namespace App\Sharp\TestModels;

use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class TestModelFormReadonly extends TestModelForm
{
    public function buildFormFields(FieldsContainer $formFields): void
    {
        parent::buildFormFields($formFields);

        collect($formFields->getFields())
            ->each(fn (SharpFormField $field) => $field->setReadonly());
    }
}
