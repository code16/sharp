<?php

namespace Code16\Sharp\EntityList\Traits\Utils;

use Illuminate\Support\Collection;

trait CommonCommandUtils
{
    protected function appendCommandsToConfig(Collection $commandHandlers, array &$config, $instanceId = null): void
    {
        $commandHandlers
            ->each(function($handler, $commandName) use(&$config, $instanceId) {
                $formFields = $handler->form();
                $formLayout = $formFields ? $handler->formLayout() : null;
                $hasFormInitialData = $formFields
                    ? is_method_implemented_in_concrete_class($handler, 'initialData')
                    : false;

                $config["commands"][$handler->type()][$handler->groupIndex()][] = [
                    "key" => $commandName,
                    "label" => $handler->label(),
                    "description" => $handler->description(),
                    "type" => $handler->type(),
                    "confirmation" => $handler->confirmationText() ?: null,
                    "modal_title" => $handler->formModalTitle() ?: null,
                    "form" => $formFields ? array_merge(
                        [
                            "fields" => $formFields,
                            "layout" => $formLayout,
                        ], method_exists($handler, 'getDataLocalizations')
                            ? ["locales" => $handler->getDataLocalizations()]
                            : []
                    ) : null,
                    "fetch_initial_data" => $hasFormInitialData,
                    "authorization" => $instanceId
                        ? $handler->authorizeFor($instanceId)
                        : $handler->getGlobalAuthorization()
                ];
            });
    }
}