<?php

namespace Code16\Sharp\Http\Api\Commands;

use Code16\Sharp\EntityList\Commands\InstanceCommand;

trait HandleCommandForm
{
    protected function getInstanceCommandForm(InstanceCommand $commandHandler): ?array
    {
        if(!count($formFields = $commandHandler->form())) {
            return null;
        }
        
        $locales = $commandHandler->getDataLocalizations();
        
        return array_merge(
            [
                'config' => $commandHandler->commandFormConfig(),
                'fields' => $formFields,
                'layout' => $commandHandler->formLayout(),
            ],
            $locales ? ["locales" => $locales] : []
        );
    }
}
