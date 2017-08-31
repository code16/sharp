<?php

namespace App\Sharp;

use App\Spaceship;
use App\Travel;
use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteField;
use Code16\Sharp\Form\Fields\SharpFormDateField;
use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\SharpForm;

class TravelSharpForm extends SharpForm
{
    use WithSharpFormEloquentUpdater;

    function buildFormFields()
    {
        $this->addField(
            SharpFormDateField::make("departure_date")
                ->setHasTime(true)
                ->setLabel("Departure date")

        )->addField(
            SharpFormSelectField::make("spaceship_id",
                Spaceship::orderBy("name")->get()->pluck("name", "id")->all()
            )
                ->setLabel("Spaceship")
                ->setDisplayAsDropdown()

        )->addField(
            SharpFormTextField::make("destination")
                ->setLabel("Destination")

        )->addField(
            SharpFormListField::make("delegates")
                ->setLabel("Travel delegates")
                ->setAddable()
                ->setRemovable()
                ->addItemField(
                    SharpFormAutocompleteField::make("<item>", "remote")
                        ->setLabel("Passenger")
                        ->setPlaceholder("test")
                        ->setListItemInlineTemplate("{{ name }}")
                        ->setResultItemTemplatePath("sharp/templates/delegate_result.vue")
                        ->setRemoteEndpoint(url('/passengers'))
                )
        );
    }

    function buildFormLayout()
    {
        $this->addColumn(5, function(FormLayoutColumn $column) {
            $column->withSingleField("departure_date")
                ->withSingleField("destination");
        })->addColumn(7, function(FormLayoutColumn $column) {
            $column->withSingleField("spaceship_id")
                ->withSingleField("delegates", function(FormLayoutColumn $listItem) {
                    $listItem->withSingleField("<item>");
                });
        });
    }

    function find($id): array
    {
        return $this
//            ->setCustomTransformer("delegates", function($value, $travel) {
//                return $travel->delegates->map(function($passenger) {
//                    $passenger["num"] = $passenger["id"];
//                    unset($passenger["id"]);
//                    return $passenger;
//                });
//            })
            ->transform(
            Travel::with(["spaceship", "delegates"])->findOrFail($id)
        );
    }

    function update($id, array $data)
    {
        $instance = $id ? Travel::findOrFail($id) : new Travel;

        $this->save($instance, $data);
    }

    function delete($id)
    {
        Travel::findOrFail($id)->delete();
    }
}