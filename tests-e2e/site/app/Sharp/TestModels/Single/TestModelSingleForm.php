<?php

namespace App\Sharp\TestModels\Single;

use App\Models\TestModel;
use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Form\SharpSingleForm;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class TestModelSingleForm extends SharpSingleForm
{
    public function buildFormFields(FieldsContainer $formFields): void
    {
        // TODO: Implement buildFormFields() method.
    }

    public function buildFormLayout(FormLayout $formLayout): void
    {
        // TODO: Implement buildFormLayout() method.
    }

    protected function findSingle()
    {
        return $this->transform(TestModel::first());
    }

    protected function updateSingle(array $data) {}
}
