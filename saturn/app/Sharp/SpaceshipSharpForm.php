<?php

namespace App\Sharp;

use Code16\Sharp\Form\BuildsSharpFormFields;
use Code16\Sharp\Form\BuildsSharpFormLayout;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Form\SharpFormData;
use Code16\Sharp\Http\WithSharpFormContext;

class SpaceshipSharpForm implements SharpForm, SharpFormData
{
    use WithSharpFormContext;

    use BuildsSharpFormFields;

    use BuildsSharpFormLayout;

//    use WithSharpEloquentUpdater;

    function buildForm()
    {
        $this->addField(
            SharpFormTextField::make("name")
                ->setLabel("Name")
        );

//        if($this->context()->isUpdate()) {
//
//        }

//        $this->addTab("Content")
//            ->addColumn()...

        $this->addColumn(5)
            ->withSingleField("name")
            ->withFields("first_name|4,6", "last_name|8,6")
            ->withFieldset("dates", function($fieldset) {
                return $fieldset->withFields("start_date", "end_date");
            });
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