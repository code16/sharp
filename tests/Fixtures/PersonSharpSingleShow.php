<?php

namespace Code16\Sharp\Tests\Fixtures;

use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayoutColumn;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Show\SharpSingleShow;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class PersonSharpSingleShow extends SharpSingleShow
{
    function buildShowFields(FieldsContainer $showFields): void
    {
        $showFields->addField(SharpShowTextField::make("name"));
    }

    function buildShowLayout(): void
    {
        $this
            ->addSection("Identity", function(ShowLayoutSection $section) {
                $section
                    ->addColumn(6, function(ShowLayoutColumn $column) {
                        $column->withSingleField("name");
                    });
            });
    }

    function findSingle(): array
    {
        return ["name" => "John Wayne", "job" => "actor", "state" => "active"];
    }
}