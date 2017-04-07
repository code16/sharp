<?php

namespace Code16\Sharp\Tests\Fixtures;

use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Form\SharpFormException;

class PersonSharpForm extends SharpForm
{
    function buildFormFields()
    {
        $this->addField(SharpFormTextField::make("name"));
    }

    function buildFormLayout()
    {
        $this->addColumn(6)
            ->withSingleField("name");
    }

    function find($id): array
    {
        return ["name" => "John Wayne"];
    }

    function update($id, array $data): bool
    {
        if(!intval($id)) {
            throw new SharpFormException("$id is not a valid id");
        }

        return true;
    }

    function store(array $data): bool
    {
        return true;
    }

    function delete($id): bool
    {
        return true;
    }
}