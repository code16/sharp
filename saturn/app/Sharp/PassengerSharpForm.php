<?php

namespace App\Sharp;

use App\Passenger;
use App\Travel;
use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Fields\SharpFormDateField;
use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class PassengerSharpForm extends SharpForm
{
    use WithSharpFormEloquentUpdater;
    protected ?string $formValidatorClass = PassengerSharpValidator::class;

    function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormTextField::make("name")
                    ->setLabel("Name")
            )
            ->addField(
                SharpFormDateField::make("birth_date")
                    ->setHasTime(false)
                    ->setLabel("Birth date")
            )
            ->addField(
                SharpFormSelectField::make("gender", ["M"=>"Mr", "F"=>"Mrs"])
                    ->setDisplayAsDropdown()
                    ->setLabel("Gender")
                    ->setClearable()
            )
            ->addField(
                SharpFormSelectField::make("travel_category", [
                    "Business"=>"Business",
                    "First class"=>"First class",
                    "Classic"=>"Classic",
                    "Third class"=>"Third class",
                ])
                    ->setDisplayAsDropdown()
                    ->setLabel("Travel category")
            )
            ->addField(
                SharpFormSelectField::make("travel_id",
                    Travel::orderBy("departure_date")->get()->map(function($travel) {
                        return [
                            "id" => $travel->id,
                            "label" => $travel->departure_date->format("Y-m-d (H:i)")
                                . " — " . $travel->destination];
                        })
                    ->all()
                )
                    ->setLabel("Travel")
                    ->setDisplayAsList()
            );
    }
    
    public function buildFormConfig(): void
    {
        $this->configurePageAlert(
            'Careful: editing a passenger in category {{category}} could lead to problems.',
            static::$pageAlertLevelWarning,
            'html_help'
        );
    }

    function buildFormLayout(FormLayout $formLayout): void
    {
        $formLayout
            ->addColumn(6, function(FormLayoutColumn $column) {
                $column->withFields("gender|4", "name|8")
                    ->withSingleField("birth_date");
            })
            ->addColumn(6, function(FormLayoutColumn $column) {
                $column->withSingleField("travel_id")
                    ->withSingleField("travel_category");
            });
    }

    function find($id): array
    {
        return $this
            ->setCustomTransformer('html_help', function($value, Passenger $passenger) {
                return [
                    "category" => $passenger->travel_category
                ];
            })
            ->transform(Passenger::with("travel")->findOrFail($id));
    }

    function update($id, array $data)
    {
        $instance = $id ? Passenger::findOrFail($id) : new Passenger;

        $this->save($instance, $data);
    }

    function delete($id): void
    {
        Passenger::findOrFail($id)->delete();
    }
}