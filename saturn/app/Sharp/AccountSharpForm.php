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

    /**
     * Build form fields using ->addField()
     *
     * @return void
     */
    function buildFormFields()
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

    /**
     * Build form layout using ->addTab() or ->addColumn()
     *
     * @return void
     */
    function buildFormLayout()
    {
        $this->addColumn(6, function(FormLayoutColumn $column) {
            return $column->withSingleField("name")
                ->withSingleField("email");
        });
    }

    /**
     * @return array
     */
    protected function findSingle()
    {
        return $this->transform(User::findOrFail(auth()->id()));
    }

    /**
     * @param array $data
     * @return mixed
     */
    protected function updateSingle(array $data)
    {
        $this->save(User::findOrFail(auth()->id()), $data);
    }
}