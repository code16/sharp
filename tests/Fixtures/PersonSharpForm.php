<?php

namespace Code16\Sharp\Tests\Fixtures;

use Code16\Sharp\Exceptions\Form\SharpApplicativeException;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class PersonSharpForm extends SharpForm
{
    function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields->addField(SharpFormTextField::make("name"));
    }

    function buildFormLayout(FormLayout $formLayout): void
    {
        $formLayout->addColumn(6, function(FormLayoutColumn $column) {
            return $column->withSingleField("name");
        });
    }

    function find($id): array
    {
        return ["name" => "John Wayne", "job" => "actor"];
    }

    function update($id, array $data): bool
    {
        if(!is_null($id) && !intval($id)) {
            // Throw an applicative exception
            throw new SharpApplicativeException("$id is not a valid id");
        }

        return true;
    }

    function delete($id): void
    {
    }

    function create(): array
    {
        return [
            "name" => "default name"
        ];
    }
}