<?php

namespace Code16\Sharp\Http\Api\Commands;

use Code16\Sharp\Dashboard\Commands\DashboardCommand;
use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\Form\Layout\FormLayout;

trait HandleCommandForm
{
    protected function getCommandForm(InstanceCommand|EntityCommand|DashboardCommand $commandHandler): array
    {
        if (! count($formFields = $commandHandler->form())) {
            return [];
        }

        $locales = $commandHandler->getDataLocalizations();

        return array_merge(
            [
                'fields' => $formFields,
                'layout' => $commandHandler->formLayout(),
            ],
            $locales ? ['locales' => $locales] : [],
        );
    }
}
