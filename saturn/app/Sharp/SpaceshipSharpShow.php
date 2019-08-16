<?php

namespace App\Sharp;

use App\Spaceship;
use Code16\Sharp\Show\Fields\SharpShowTextField;
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
            ->addSection(function(ShowLayoutSection $section) {
                $section
                    ->setTitle("Identity")
                    ->addColumn(7, function(ShowLayoutColumn $column) {
                        $column->withSingleLabel("name");
                    });

            });
    }

    function find($id): array
    {
        return $this->transform(Spaceship::findOrFail($id));
    }
}