<?php

namespace Code16\Sharp\EntityList\Commands\Wizards;

use Illuminate\Support\Str;

trait IsWizardCommand
{
    protected ?CommandWizardContext $commandWizardContext = null;
    private string $key;
    
    protected function getWizardContext(): CommandWizardContext
    {
        if(!$this->commandWizardContext) {
            $this->commandWizardContext = session()->get(sprintf("CWC.%s.%s", get_class($this), $this->getKey()));
            if(!$this->commandWizardContext) {
                $this->commandWizardContext = new CommandWizardContext();
            }
        }
        
        return $this->commandWizardContext;
    }

    protected function toStep(string $step): array
    {
        if($this->commandWizardContext) {
            session()->put(sprintf("CWC.%s.%s", get_class($this), $this->getKey()), $this->commandWizardContext);
        }
        
        return [
            'action' => 'step',
            'step' => "{$step}:{$this->key}",
        ];
    }

    protected function extractStepFromRequest(): ?string
    {
        if($step = request()->get('command_step')) {
            list($step, $this->key) = explode(':', $step);

            return $step;
        }

        return null;
    }

    protected function getKey(): string
    {
        if(!isset($this->key)) {
            $this->key = Str::random(5);
        }

        return $this->key;
    }
}
