<?php

namespace Code16\Sharp\Tests\Fixtures;

use Code16\Sharp\EntityList\Commands\EntityState;
use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayoutColumn;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Show\SharpShow;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class PersonSharpShow extends SharpShow
{
    function buildShowFields(FieldsContainer $showFields): void
    {
        $showFields->addField(SharpShowTextField::make("name"));
    }
    
    function getInstanceCommands(): ?array
    {
        return [
            "test_command" => new class extends InstanceCommand {
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
            },
            
        ];
    }

    function buildShowConfig(): void
    {
        $this
            ->configureEntityState("state", new class extends EntityState {
                protected function buildStates(): void
                {
                    $this->addState("active", "Label", "blue");
                }
                protected function updateState($instanceId, $stateId): array
                {
                }
                public function authorizeFor($instanceId): bool
                {
                    return $instanceId < 10;
                }
            });
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

    function find($id): array
    {
        return ["name" => "John Wayne", "job" => "actor", "state" => "active"];
    }
}