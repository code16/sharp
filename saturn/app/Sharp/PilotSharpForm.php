<?php

namespace App\Sharp;

use App\Pilot;
use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\SharpForm;

class PilotSharpForm extends SharpForm
{
    use WithSharpFormEloquentUpdater;

    function buildFormFields()
    {
        $this->addField(
            SharpFormTextField::make("name")
                ->setLabel("Name")
        );
    }

    function buildFormLayout()
    {
        $this->addColumn(6, function(FormLayoutColumn $column) {
            $column->withSingleField("name");
        });
    }

    function find($id): array
    {
        return $this->transform(Pilot::findOrFail($id));
    }

    function update($id, array $data)
    {
        $instance = $id ? Pilot::findOrFail($id) : new Pilot;

        $this->save($instance, $data);
    }

    function delete($id)
    {
        Pilot::findOrFail($id)->delete();
    }
}