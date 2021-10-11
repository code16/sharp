<?php

namespace App\Sharp;

use App\Pilot;
use Code16\Sharp\Form\Fields\SharpFormNumberField;
use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class PilotSeniorSharpForm extends PilotJuniorSharpForm
{
    function buildFormFields(FieldsContainer $formFields): void
    {
        parent::buildFormFields($formFields);

        $formFields->addField(
            SharpFormNumberField::make("xp")
                ->setLabel("Experience (in years)")
        );
    }

    function buildFormLayout(FormLayout $formLayout): void
    {
        parent::buildFormLayout($formLayout);

        $formLayout->addColumn(6, function(FormLayoutColumn $column) {
            $column->withSingleField("xp");
        });
    }
    
    function buildFormConfig(): void
    {
        $this->configureBreadcrumbCustomLabelAttribute("name");
    }

    function update($id, array $data)
    {
        $instance = $id ? Pilot::findOrFail($id) : new Pilot;
        $this->save($instance, $data + ["role" => "sr"]);
        return $instance->id;
    }
}