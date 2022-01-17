<?php

namespace Code16\Sharp\Tests\Fixtures;

use Code16\Sharp\Exceptions\Form\SharpApplicativeException;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class PersonSharpForm extends SharpForm
{
    protected function getFormValidatorClass(): ?string
    {
        return app(SharpEntityManager::class)->entityFor('person')->validatorForTest;
    }

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields->addField(SharpFormTextField::make('name'));
    }

    public function buildFormLayout(FormLayout $formLayout): void
    {
        $formLayout->addColumn(6, function (FormLayoutColumn $column) {
            return $column->withSingleField('name');
        });
    }

    public function find($id): array
    {
        return ['name' => 'John Wayne', 'job' => 'actor'];
    }

    public function update($id, array $data): bool
    {
        if (!is_null($id) && !intval($id)) {
            // Throw an applicative exception
            throw new SharpApplicativeException("$id is not a valid id");
        }

        return true;
    }

    public function delete($id): void
    {
    }

    public function create(): array
    {
        return [
            'name' => 'default name',
        ];
    }
}
