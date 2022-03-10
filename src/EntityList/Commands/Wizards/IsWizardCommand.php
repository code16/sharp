<?php

namespace Code16\Sharp\EntityList\Commands\Wizards;

trait IsWizardCommand
{
    protected ?CommandWizardContext $commandWizardContext = null;
    
    protected function getWizardContext(): CommandWizardContext
    {
        if(!$this->commandWizardContext) {
            $this->commandWizardContext = session()->get("CWC." . get_class($this));
            if(!$this->commandWizardContext) {
                $this->commandWizardContext = new CommandWizardContext();
            }
        }
        
        return $this->commandWizardContext;
    }

    protected function toStep(string $step): array
    {
        if($this->commandWizardContext) {
            session()->put("CWC." . get_class($this), $this->commandWizardContext);
        }
        
        return [
            'action' => 'step',
            'step' => $step,
        ];
    }
}
