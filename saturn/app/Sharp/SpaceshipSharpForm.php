<?php

namespace App\Sharp;

use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Form\SharpFormData;

class SpaceshipSharpForm implements SharpForm, SharpFormData
{
    use WithSharpFormContext;

    use WithSharpEloquentUpdater;

    function fields(): array
    {
        // Context is sent by the front-end as param
        // /sharp/api/form/{key}?create
        // /sharp/api/form/{key}?update={id}
        if($this->context()->isUpdate()) {

        }

        if($this->context()->isCreation()) {

        }

        if($this->context()->updateId()) {

        }
    }

    /**
     * Retrieve a Model for the form and pack all its data as JSON.
     *
     * @param $id
     * @return array
     */
    function get($id): array
    {
        return Spaceship::findOrFail($id)->toArray();
    }

    function update($id, array $data): bool
    {
    }

    function store(array $data): bool
    {
    }

    function delete($id): bool
    {
    }

}