<?php

namespace Code16\Sharp\Http\Controllers\Api\Commands;

use Code16\Sharp\Dashboard\Commands\DashboardCommand;
use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\Commands\InstanceCommand;

trait HandlesCommandForm
{
    protected function getCommandForm(InstanceCommand|EntityCommand|DashboardCommand $commandHandler, ?array $formData): array
    {
        if (! count($formFields = $commandHandler->form())) {
            return [];
        }

        $locales = $commandHandler->getDataLocalizations();

        return [
            'fields' => $formFields,
            'layout' => $commandHandler->formLayout(),
            'data' =>  $formData ? $commandHandler->applyFormatters($formData) : null,
            'pageAlert' => $commandHandler->pageAlert($formData),
            'config' => [
                'title' => $commandHandler->getFormModalTitle($formData) ?: $commandHandler->label(),
                'description' => $commandHandler->getFormModalDescription($formData) ?: $commandHandler->getDescription(),
                'buttonLabel' => $commandHandler->getFormModalButtonLabel(),
            ],
            ...$locales ? [
                'locales' => $locales,
            ] : [],
        ];
    }
}
