<?php

namespace Code16\Sharp\Tests\Fixtures;

use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\SharpShow;

class PersonSharpShow extends SharpShow
{
    /**
     * Build form fields using ->addField()
     *
     * @return void
     */
    function buildShowFields()
    {
        $this->addField(SharpShowTextField::make("name"));
    }

    /**
     * Build form layout using ->addSection()
     *
     * @return void
     */
    function buildShowLayout()
    {
        $this
            ->addSection(function(ShowLayoutSection $section) {
                $section
                    ->setTitle("Identity")
                    ->addColumn(6, function(ShowLayoutColumn $column) {
                        $column->withSingleLabel("name");
                    });

            });
    }

    function find($id): array
    {
        return ["name" => "John Wayne", "job" => "actor"];
    }
}