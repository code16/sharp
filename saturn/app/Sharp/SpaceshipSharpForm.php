<?php

namespace App\Sharp;

use Code16\Sharp\Form\BuildsSharpFormFields;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Form\SharpFormData;
use Code16\Sharp\Http\WithSharpFormContext;

class SpaceshipSharpForm implements SharpForm, SharpFormData
{
    use WithSharpFormContext;

    use BuildsSharpFormFields;

//    use WithSharpEloquentUpdater;

    function buildForm(): void
    {
        $this->addField(
            SharpFormTextField::make("name")
                ->setLabel("Name")
        );

//        if($this->context()->isUpdate()) {
//
//        }
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