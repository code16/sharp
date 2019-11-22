<?php

namespace Code16\Sharp\Tests\Fixtures;

use Code16\Sharp\EntityList\Commands\EntityState;
use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Show\Fields\SharpShowTextField;
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
            })
            ->addInstanceCommand("unauthorized_command", new class extends InstanceCommand {
                public function label(): string
                {
                    return "Label";
                }
                public function execute($instanceId, array $data = []): array
                {
                }
                public function authorizeFor($instanceId): bool
                {
                    return false;
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
                    ->addColumn(6, function(FormLayoutColumn $column) {
                        $column->withSingleField("name");
                    });
            });
    }

    function find($id): array
    {
        return ["name" => "John Wayne", "job" => "actor"];
    }
}