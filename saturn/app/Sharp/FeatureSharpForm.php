<?php

namespace App\Sharp;

use App\Feature;
use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\SharpForm;

class FeatureSharpForm extends SharpForm
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
        $this->addColumn(5, function(FormLayoutColumn $column) {
            $column->withSingleField("name");
        });
    }

    function find($id): array
    {
        return $this->transform(
            Feature::findOrFail($id)
        );
    }

    function update($id, array $data)
    {
        $instance = $id ? Feature::findOrFail($id) : new Feature;

        $this->save($instance, $data);
    }

    function delete($id)
    {
        Feature::findOrFail($id)->delete();
    }
}