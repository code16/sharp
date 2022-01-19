<?php

namespace App\Sharp;

use App\User;
use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\SharpSingleForm;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class AccountSharpForm extends SharpSingleForm
{
    use WithSharpFormEloquentUpdater;

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormTextField::make('name')
                    ->setLabel('Name'),
            )
            ->addField(
                SharpFormTextField::make('email')
                    ->setLabel('Email address'),
            );
    }

    public function buildFormLayout(FormLayout $formLayout): void
    {
        $formLayout->addColumn(6, function (FormLayoutColumn $column) {
            return $column->withSingleField('name')
                ->withSingleField('email');
        });
    }

    protected function findSingle()
    {
        return $this->transform(User::findOrFail(auth()->id()));
    }

    protected function updateSingle(array $data)
    {
        $this->save(User::findOrFail(auth()->id()), $data);
    }
}
