<?php

namespace Code16\Sharp\Tests\Fixtures;

use Code16\Sharp\EntityList\Commands\EntityState;
use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayoutColumn;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
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

    function buildShowConfig()
    {
        $this
            ->addInstanceCommand("test_command", new class extends InstanceCommand {
                public function label(): string
                {
                    return "Label";
                }
                public function execute($instanceId, array $data = []): array
                {
                }
                public function authorizeFor($instanceId): bool
                {
                    return $instanceId < 10;
                }
            })
            ->setEntityState("state", new class extends EntityState {
                protected function buildStates()
                {
                    $this->addState("active", "Label", "blue");
                }
                protected function updateState($instanceId, $stateId)
                {
                }
                public function authorizeFor($instanceId): bool
                {
                    return $instanceId < 10;
                }
            });
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

    function find($id): array
    {
        return ["name" => "John Wayne", "job" => "actor", "state" => "active"];
    }
}