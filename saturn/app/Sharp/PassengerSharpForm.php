<?php

namespace App\Sharp;

use App\Passenger;
use App\Travel;
use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Fields\SharpFormDateField;
use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\SharpForm;

class PassengerSharpForm extends SharpForm
{
    use WithSharpFormEloquentUpdater;

    public function buildFormFields(): void
    {
        $this->addField(
            SharpFormTextField::make('name')
                ->setLabel('Name')
        )->addField(
            SharpFormDateField::make('birth_date')
                ->setHasTime(false)
                ->setLabel('Birth date')
        )->addField(
            SharpFormSelectField::make('gender', ['M'=>'Mr', 'F'=>'Mrs'])
                ->setDisplayAsDropdown()
                ->setLabel('Gender')
                ->setClearable()
        )->addField(
            SharpFormSelectField::make('travel_category', [
                'Business'   => 'Business',
                'First class'=> 'First class',
                'Classic'    => 'Classic',
                'Third class'=> 'Third class',
            ])
                ->setDisplayAsDropdown()
                ->setLabel('Travel category')
        )->addField(
            SharpFormSelectField::make(
                'travel_id',
                Travel::orderBy('departure_date')->get()->map(function ($travel) {
                    return [
                        'id'    => $travel->id,
                        'label' => $travel->departure_date->format('Y-m-d (H:i)')
                            .' — '.$travel->destination,
                    ];
                })->all()
            )
                ->setLabel('Travel')
                ->setDisplayAsList()
        );
    }

    public function buildFormLayout(): void
    {
        $this->addColumn(6, function (FormLayoutColumn $column) {
            $column->withFields('gender|4', 'name|8')
                ->withSingleField('birth_date');
        })->addColumn(6, function (FormLayoutColumn $column) {
            $column->withSingleField('travel_id')
                ->withSingleField('travel_category');
        });
    }

    public function find($id): array
    {
        return $this->transform(Passenger::with('travel')->findOrFail($id));
    }

    public function update($id, array $data)
    {
        $instance = $id ? Passenger::findOrFail($id) : new Passenger();

        $this->save($instance, $data);
    }

    public function delete($id): void
    {
        Passenger::findOrFail($id)->delete();
    }
}
