<?php

namespace App\Sharp;

use App\Spaceship;
use App\SpaceshipType;
use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentTransformer;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteField;
use Code16\Sharp\Form\Fields\SharpFormDateField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\SharpForm;

class SpaceshipSharpForm extends SharpForm
{
    use WithSharpFormEloquentUpdater;

    use WithSharpFormEloquentTransformer;

    function buildFormFields()
    {
        $this->addField(
            SharpFormTextField::make("name")
                ->setLabel("Name")
        );

        $this->addField(
            SharpFormTextField::make("capacity")
                ->setLabel("Capacity (thousands)")
        );

        $this->addField(
            SharpFormDateField::make("construction_date")
                ->setLabel("Construction date")
                ->setHasTime(false)
        );

//        $this->addField(
//            SharpFormAutocompleteField::make("type_id", "local")
//                ->setLabel("Ship type")
//                ->setListItemTemplatePath("/sharp/templates/spaceshipType_list")
//                ->setResultItemTemplatePath("/sharp/templates/spaceshipType_result")
//                ->setLocalValues(
//                    SpaceshipType::orderBy("label")->get()->pluck("label", "id")->all()
//                )
//        );
    }

    function buildFormLayout()
    {
        $this->addColumn(6)
            ->withSingleField("name")
            ->withSingleField("type_id")
            ->withFieldset("Technical details", function($fieldset) {
                return $fieldset->withFields("capacity|4,6", "construction_date|8,6");
            });
    }

//    function create()
//    {
//        return $this->transform(new Spaceship);
//    }

    function find($id): array
    {
        return $this->setCustomTransformer("capacity", function($spaceship) {
            return $spaceship->capacity / 1000;

        })->transform(
            Spaceship::findOrFail($id)
        );
    }

    function update($id, array $data): bool
    {
        $instance = $id ? Spaceship::findOrFail($id) : new Spaceship;

        return $this->setCustomValuator("capacity", function($spaceship, $value) {
            return $value * 1000;

        })->save($instance, $data);
    }

    function delete($id): bool
    {
        return true;
    }
}