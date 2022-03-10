<?php

namespace Code16\Sharp\EntityList\Commands\Wizards;

use Illuminate\Support\Str;

trait IsWizardCommand
{
    protected ?WizardCommandContext $wizardCommandContext = null;
    private string $key;
    
    protected function getWizardContext(): WizardCommandContext
    {
        if(!$this->wizardCommandContext) {
            $this->wizardCommandContext = session()->get(sprintf("CWC.%s.%s", get_class($this), $this->getKey()));
            if(!$this->wizardCommandContext) {
                $this->wizardCommandContext = new WizardCommandContext();
            }
        }
        
        return $this->wizardCommandContext;
    }

    protected function toStep(string $step): array
    {
        if($this->wizardCommandContext) {
            session()->put(sprintf("CWC.%s.%s", get_class($this), $this->getKey()), $this->wizardCommandContext);
        }
        
        return [
            'action' => 'step',
            'step' => "{$step}:{$this->getKey()}",
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
