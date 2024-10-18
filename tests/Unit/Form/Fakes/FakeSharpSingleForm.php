<?php

namespace Code16\Sharp\Tests\Unit\Form\Fakes;

use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Form\SharpSingleForm;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class FakeSharpSingleForm extends SharpSingleForm
{
    public function buildFormFields(FieldsContainer $formFields): void
    {
    }

    public function buildFormLayout(FormLayout $formLayout): void
    {
    }

    protected function findSingle()
    {
    }

    protected function updateSingle(array $data)
    {
    }
}
