<?php

namespace Code16\Sharp\Tests\Unit\Form\Fakes;

use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class FakeSharpForm extends SharpForm
{
    public function find(mixed $id): array
    {
        return [];
    }

    public function update(mixed $id, array $data)
    {
    }

    public function buildFormFields(FieldsContainer $formFields): void
    {
    }

    public function buildFormLayout(FormLayout $formLayout): void
    {
    }
}