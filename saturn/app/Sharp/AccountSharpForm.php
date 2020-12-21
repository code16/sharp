<?php

namespace App\Sharp;

use App\User;
use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\SharpSingleForm;

class AccountSharpForm extends SharpSingleForm
{
    use WithSharpFormEloquentUpdater;

    function buildFormFields(): void
    {
        $this
            ->addField(
                SharpFormTextField::make("name")
                    ->setLabel("Name")
            )->addField(
                SharpFormTextField::make("email")
                    ->setLabel("Email address")
            );
    }

    function buildFormLayout(): void
    {
        $this->addColumn(6, function(FormLayoutColumn $column) {
            return $column->withSingleField("name")
                ->withSingleField("email");
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