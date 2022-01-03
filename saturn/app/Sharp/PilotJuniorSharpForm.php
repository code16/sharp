<?php

namespace App\Sharp;

use App\Pilot;
use App\Sharp\CustomFormFields\SharpCustomFormFieldTextIcon;
use App\Spaceship;
use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class PilotJuniorSharpForm extends SharpForm
{
    use WithSharpFormEloquentUpdater;

    function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields->addField(
            SharpCustomFormFieldTextIcon::make("name")
                ->setLabel("Name")
                ->setHelpMessage("This input is an example of a custom form field (SharpCustomFormFieldTextIcon)")
                ->setIcon("fa-user")
        );
    }

    function buildFormLayout(FormLayout $formLayout): void
    {
        $formLayout->addColumn(6, function(FormLayoutColumn $column) {
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

        if(currentSharpRequest()->isCreation()) {
            if($breadcrumbItem = currentSharpRequest()->getPreviousShowFromBreadcrumbItems()) {
                if ($breadcrumbItem->entityKey() === "spaceship") {
                    Spaceship::findOrFail($breadcrumbItem->instanceId())
                        ->pilots()
                        ->attach($pilot->id);
                }
            }
        }
        
        return $pilot->id;
    }

    function buildFormConfig(): void
    {
        $this->configureBreadcrumbCustomLabelAttribute("name");
    }

    function delete($id): void
    {
        Pilot::findOrFail($id)->delete();
    }

    public function getFormValidatorClass(): ?string
    {
        return PilotJuniorSharpValidator::class;
    }
}
