<?php

namespace App\Sharp;

use App\Spaceship;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Show\SharpShow;

class SpaceshipSharpShow extends SharpShow
{
    function buildShowFields()
    {
        $this->addField(
            SharpShowTextField::make("name")
                ->prependLabelWith("Spaceship name: ")
        );
    }

    function buildShowLayout()
    {
        $this
            ->addSection('Identity', function(ShowLayoutSection $section) {
                $section
                    ->addColumn(7, function(FormLayoutColumn $column) {
                        $column->withSingleField("name");
                    });
            });
    }

    function find($id): array
    {
        return $this->transform(Spaceship::findOrFail($id));
    }
}