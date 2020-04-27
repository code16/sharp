<?php

namespace App\Sharp;

use App\Pilot;
use App\Sharp\CustomFormFields\SharpCustomFormFieldTextIcon;
use App\Spaceship;
use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Http\WithSharpContext;

class PilotJuniorSharpForm extends SharpForm
{
    use WithSharpFormEloquentUpdater, WithSharpContext;

    function buildFormFields()
    {
        $this->addField(
            SharpCustomFormFieldTextIcon::make("name")
                ->setLabel("Name")
                ->setHelpMessage("This input is an example of a custom form field (SharpCustomFormFieldTextIcon)")
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
        $pilot = $id ? Pilot::findOrFail($id) : new Pilot;

        $pilot = $this->save($pilot, $data + ["role" => "jr"]);

        if($this->context()->isCreation()) {
            if($breadcrumb = $this->context()->getPreviousPageFromBreadcrumb("show")) {
                list($type, $entityKey, $instanceId) = $breadcrumb;
                if ($entityKey == "spaceship") {
                    Spaceship::findOrFail($instanceId)->pilots()->attach($pilot->id);
                }
            }
        }
    }

    function delete($id)
    {
        Pilot::findOrFail($id)->delete();
    }
}