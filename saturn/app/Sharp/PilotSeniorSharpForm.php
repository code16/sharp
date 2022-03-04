<?php

namespace App\Sharp;

use App\Pilot;
use Code16\Sharp\Form\Fields\SharpFormNumberField;
use Code16\Sharp\Form\Layout\FormLayoutColumn;

class PilotSeniorSharpForm extends PilotJuniorSharpForm
{
    public function buildFormFields(): void
    {
        parent::buildFormFields();

        $this->addField(
            SharpFormNumberField::make('xp')
                ->setLabel('Experience (in years)')
        );
    }

    public function buildFormLayout(): void
    {
        parent::buildFormLayout();

        $this->addColumn(6, function (FormLayoutColumn $column) {
            $column->withSingleField('xp');
        });
    }

    public function buildFormConfig(): void
    {
        $this->setBreadcrumbCustomLabelAttribute('name');
    }

    public function update($id, array $data)
    {
        $instance = $id ? Pilot::findOrFail($id) : new Pilot();

        $this->save($instance, $data + ['role' => 'sr']);

        return $instance->id;
    }
}
