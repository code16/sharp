<?php

namespace App\Sharp;

use Code16\Sharp\Form\BuildsSharpFormFields;
use Code16\Sharp\Form\BuildsSharpFormLayout;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteField;
use Code16\Sharp\Form\Fields\SharpFormDateField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Form\SharpFormData;

class SpaceshipSharpForm implements SharpForm, SharpFormData
{
    use BuildsSharpFormFields;

    use BuildsSharpFormLayout;

//    use WithSharpEloquentUpdater;

    /**
     * Get the fields array representation.
     *
     * @return array
     */
    function fields(): array
    {
        $this->addField(
            SharpFormTextField::make("name")
                ->setLabel("Name")
        );

        $this->addField(
            SharpFormTextField::make("capacity")
                ->setLabel("Capacity")
        );

        $this->addField(
            SharpFormDateField::make("construction_date")
                ->setLabel("Construction date")
                ->setHasTime(false)
        );

        $this->addField(
            SharpFormAutocompleteField::make("type_id", "local")
                ->setLabel("Ship type")
                ->setListItemTemplatePath("/sharp/templates/spaceshipType_list")
                ->setResultItemTemplatePath("/sharp/templates/spaceshipType_result")
                ->setLocalValues(
                    SpaceshipType::orderBy("label")->get()->pluck("label", "id")
                )
        );

        return $this->buildForm();
    }

    /**
     * Return the form fields layout.
     *
     * @return array
     */
    function formLayout(): array
    {
        $this->addColumn(6)
            ->withSingleField("name")
            ->withSingleField("type_id")
            ->withFieldset("Technical details", function($fieldset) {
                return $fieldset->withFields("capacity|4,6", "construction_date|8,6");
            });

        return $this->buildLayout();
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