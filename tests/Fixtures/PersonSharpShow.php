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
    public function buildShowFields(): void
    {
        $this->addField(SharpShowTextField::make('name'));
    }

    public function buildShowConfig(): void
    {
        $this
            ->addInstanceCommand('test_command', new class() extends InstanceCommand {
                public function label(): string
                {
                    return 'Label';
                }

                public function execute($instanceId, array $data = []): array
                {
                }

                public function authorizeFor($instanceId): bool
                {
                    return $instanceId < 10;
                }
            })
            ->setEntityState('state', new class() extends EntityState {
                protected function buildStates(): void
                {
                    $this->addState('active', 'Label', 'blue');
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

    public function buildShowLayout(): void
    {
        $this
            ->addSection('Identity', function (ShowLayoutSection $section) {
                $section
                    ->addColumn(6, function (ShowLayoutColumn $column) {
                        $column->withSingleField('name');
                    });
            });
    }

    public function find($id): array
    {
        return ['name' => 'John Wayne', 'job' => 'actor', 'state' => 'active'];
    }
}
