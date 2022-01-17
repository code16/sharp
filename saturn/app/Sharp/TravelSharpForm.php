<?php

namespace App\Sharp;

use App\Spaceship;
use App\Travel;
use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteField;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteListField;
use Code16\Sharp\Form\Fields\SharpFormDateField;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormGeolocationField;
use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class TravelSharpForm extends SharpForm
{
    use WithSharpFormEloquentUpdater;
    protected ?string $formValidatorClass = TravelSharpValidator::class;

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormDateField::make('departure_date')
                    ->setHasTime(true)
                    ->setLabel('Departure date')
            )
            ->addField(
                SharpFormSelectField::make(
                    'spaceship_id',
                    Spaceship::orderBy('name')->get()->pluck('name', 'id')->all()
                )
                    ->setLabel('Spaceship')
                    ->setDisplayAsDropdown()
            )
            ->addField(
                SharpFormTextField::make('destination')
                    ->setLabel('Destination')
            )
            ->addField(
                SharpFormEditorField::make('description')
                    ->setToolbar([
                        SharpFormEditorField::B,
                        SharpFormEditorField::I,
                        SharpFormEditorField::A,
                        SharpFormEditorField::SEPARATOR,
                        SharpFormEditorField::H1,
                        SharpFormEditorField::UL,
                        SharpFormEditorField::OL,
                        SharpFormEditorField::QUOTE,
                        SharpFormEditorField::CODE,
                    ])
                    ->setLabel('Description')
            )
            ->addField(
                SharpFormGeolocationField::make('destination_coordinates')
                    ->setDisplayUnitDegreesMinutesSeconds()
                    ->setGeocoding()
                    ->setInitialPosition(48.5838961, 7.742182599999978)
                    ->setApiKey(env('GMAPS_KEY', 'my-api-key'))
                    ->setLabel('Destination coordinates')
            )
            ->addField(
                SharpFormAutocompleteListField::make('delegates')
                    ->setLabel('Travel delegates')
                    ->setAddable()
                    ->setRemovable()
                    ->setItemField(
                        SharpFormAutocompleteField::make('item', 'remote')
                            ->setLabel('Passenger')
                            ->setPlaceholder('test')
                            ->setListItemInlineTemplate('{{ name }}')
                            ->setResultItemTemplatePath('sharp/templates/delegate_result.vue')
                            ->setRemoteEndpoint(url('/passengers'))
                    )
            );
    }

    public function buildFormLayout(FormLayout $formLayout): void
    {
        $formLayout
                ->addColumn(5, function (FormLayoutColumn $column) {
                    $column->withSingleField('departure_date')
                        ->withSingleField('destination')
                        ->withSingleField('destination_coordinates');
                })
                ->addColumn(7, function (FormLayoutColumn $column) {
                    $column->withSingleField('spaceship_id')
                        ->withSingleField('description')
                        ->withSingleField('delegates', function (FormLayoutColumn $listItem) {
                            $listItem->withSingleField('item');
                        });
                });
    }

    public function find($id): array
    {
        return $this->transform(
            Travel::with(['spaceship', 'delegates'])->findOrFail($id)
        );
    }

    public function update($id, array $data)
    {
        $instance = $id ? Travel::findOrFail($id) : new Travel();
        $this->save($instance, $data);
    }

    public function delete($id): void
    {
        Travel::findOrFail($id)->delete();
    }
}
