<?php

namespace App\Sharp;

use App\Spaceship;
use App\Travel;
use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteField;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteListField;
use Code16\Sharp\Form\Fields\SharpFormDateField;
use Code16\Sharp\Form\Fields\SharpFormGeolocationField;
use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Fields\SharpFormWysiwygField;
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
            SharpFormWysiwygField::make("description")
                ->setToolbar([
                    SharpFormWysiwygField::B,
                    SharpFormWysiwygField::I,
                    SharpFormWysiwygField::A,
                    SharpFormWysiwygField::S,
                    SharpFormWysiwygField::SEPARATOR,
                    SharpFormWysiwygField::H1,
                    SharpFormWysiwygField::UL,
                    SharpFormWysiwygField::OL,
                    SharpFormWysiwygField::QUOTE,
                    SharpFormWysiwygField::CODE,
                    SharpFormWysiwygField::SEPARATOR,
                    SharpFormWysiwygField::INCREASE_NESTING,
                    SharpFormWysiwygField::DECREASE_NESTING,
                    SharpFormWysiwygField::UNDO,
                    SharpFormWysiwygField::REDO,
                ])
                ->setLabel("Description")

        )->addField(
            SharpFormGeolocationField::make("destination_coordinates")
                ->setDisplayUnitDegreesMinutesSeconds()
                ->setGeocoding()
                ->setInitialPosition(48.5838961, 7.742182599999978)
                ->setApiKey(env("GMAPS_KEY", "my-api-key"))
                ->setLabel("Destination coordinates")

        )->addField(
            SharpFormAutocompleteListField::make("delegates")
                ->setLabel("Travel delegates")
                ->setAddable()
                ->setRemovable()
                ->setItemField(
                    SharpFormAutocompleteField::make("item", "remote")
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
                ->withSingleField("destination")
                ->withSingleField("destination_coordinates");
        })->addColumn(7, function(FormLayoutColumn $column) {
            $column->withSingleField("spaceship_id")
                ->withSingleField("description")
                ->withSingleField("delegates", function(FormLayoutColumn $listItem) {
                    $listItem->withSingleField("item");
                });
        });
    }

    function find($id): array
    {
        return $this->transform(
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