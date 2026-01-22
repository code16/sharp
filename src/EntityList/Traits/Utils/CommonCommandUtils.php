<?php

namespace Code16\Sharp\EntityList\Traits\Utils;

use Code16\Sharp\EntityList\Commands\Command;
use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\Utils\Icons\IconManager;
use Illuminate\Support\Collection;

trait CommonCommandUtils
{
    protected function appendCommandsToConfig(Collection $commandHandlers, array &$config, string $positionKey, array $primaryCommands, $instanceId = null): void
    {
        $commandHandlers
            ->each(function (Command $handler) use (&$config, $instanceId, $positionKey, $primaryCommands) {
                $handler->buildCommandConfig();

                $config['commands'][$positionKey][$handler->groupIndex()][] = [
                    'key' => $handler->getCommandKey(),
                    'label' => $handler->label(),
                    'description' => $handler->getDescription(),
                    'type' => $handler->type(),
                    'confirmation' => $handler->getConfirmationText()
                        ? [
                            'text' => $handler->getConfirmationText(),
                            'title' => $handler->getConfirmationTitle(),
                            'buttonLabel' => $handler->getConfirmationButtonLabel(),
                        ]
                        : null,
                    'hasForm' => count($handler->form()) > 0,
                    'authorization' => $instanceId
                        ? $handler->authorizeFor($instanceId)
                        : $handler->getGlobalAuthorization(),
                    'icon' => app(IconManager::class)->iconToArray($handler->getIcon()),
                    'primary' => in_array($handler->getCommandKey(), $primaryCommands),
                    ...$handler instanceof EntityCommand ? [
                        'instanceSelection' => $handler->getInstanceSelectionMode(),
                    ] : [],
                ];
            });

        // force JSON arrays when groupIndex starts at 1 (https://github.com/code16/sharp-dev/issues/33)
        if ($config['commands'] ?? null) {
            $config['commands'] = collect($config['commands'])
                ->map(fn ($group) => collect($group)->values()->filter())
                ->toArray();
        }
    }
}
