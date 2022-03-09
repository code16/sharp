<?php

namespace Code16\Sharp\EntityList\Commands\Wizards;

trait IsWizardCommand
{
    protected function getWizardContext(): CommandWizardContext
    {
        dd(get_class($this));
    }
    
    protected function toStep(string $step): array
    {
        return [
            'action' => 'step',
            'step' => $step,
        ];
    }
}