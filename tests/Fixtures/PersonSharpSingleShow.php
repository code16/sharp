<?php

namespace Code16\Sharp\Tests\Fixtures;

use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayoutColumn;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Show\SharpSingleShow;

class PersonSharpSingleShow extends SharpSingleShow
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
            ->addSection("Identity", function(ShowLayoutSection $section) {
                $section
                    ->addColumn(6, function(ShowLayoutColumn $column) {
                        $column->withSingleField("name");
                    });
            });
    }

    /**
     * Retrieve a Model for the form and pack all its data as JSON.
     *
     * @return array
     */
    function findSingle(): array
    {
        return ["name" => "John Wayne", "job" => "actor", "state" => "active"];
    }
}