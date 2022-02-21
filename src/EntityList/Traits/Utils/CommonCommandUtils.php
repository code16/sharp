<?php

namespace Code16\Sharp\EntityList\Traits\Utils;

use Code16\Sharp\EntityList\Commands\Command;
use Illuminate\Support\Collection;

trait CommonCommandUtils
{
    protected function appendCommandsToConfig(Collection $commandHandlers, array &$config, $instanceId = null): void
    {
        $commandHandlers
            ->each(function (Command $handler) use (&$config, $instanceId) {
                $handler->buildCommandConfig();
                $formFields = $handler->form();
                $formLayout = $formFields ? $handler->formLayout() : null;
                $hasFormInitialData = $formFields && is_method_implemented_in_concrete_class($handler, 'initialData');

                $config['commands'][$handler->type()][$handler->groupIndex()][] = [
                    'key' => $handler->getCommandKey(),
                    'label' => $handler->label(),
                    'description' => $handler->getDescription(),
                    'type' => $handler->type(),
                    'confirmation' => $handler->getConfirmationText() ?: null,
                    'modal_title' => $handler->getFormModalTitle() ?: null,
                    'form' => $formFields
                        ? array_merge(
                            [
                                'config' => $handler->commandFormConfig(),
                                'fields' => $formFields,
                                'layout' => $formLayout,
                            ],
                            method_exists($handler, 'getDataLocalizations')
                                ? ['locales' => $handler->getDataLocalizations()]
                                : [],
                        )
                        : null,
                    'fetch_initial_data' => $hasFormInitialData,
                    'authorization' => $instanceId
                        ? $handler->authorizeFor($instanceId)
                        : $handler->getGlobalAuthorization(),
                ];
            });
    }
}
