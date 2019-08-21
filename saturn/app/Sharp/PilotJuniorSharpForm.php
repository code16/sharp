<?php

namespace App\Sharp;

use App\Pilot;
use App\Sharp\CustomFormFields\SharpCustomFomFieldTextIcon;
use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\SharpForm;

class PilotJuniorSharpForm extends SharpForm
{
    use WithSharpFormEloquentUpdater;

    function buildFormFields()
    {
        $this->addField(
            SharpCustomFomFieldTextIcon::make("name")
                ->setLabel("Name")
                ->setHelpMessage("This input is an example of a custom form field (SharpCustomFomFieldTextIcon)")
                ->setIcon("fa-user")
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

        $this->save($instance, $data + ["role" => "jr"]);
    }

    function delete($id)
    {
        Pilot::findOrFail($id)->delete();
    }
}