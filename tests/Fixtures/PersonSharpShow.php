<?php

namespace Code16\Sharp\Tests\Fixtures;

use Code16\Sharp\Show\Labels\SharpShowTextLabel;
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
        $this->addField(SharpShowTextLabel::make("name"));
    }

    /**
     * Build form layout using ->addSection()
     *
     * @return void
     */
    function buildShowLayout()
    {
        // TODO: Implement buildShowLayout() method.
    }

    function find($id): array
    {
        return ["name" => "John Wayne", "job" => "actor"];
    }
}