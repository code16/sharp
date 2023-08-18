<?php

namespace Code16\Sharp\EntityList\Commands\Wizards;

use Code16\Sharp\EntityList\Commands\SingleInstanceCommand;
use Code16\Sharp\Exceptions\SharpMethodNotImplementedException;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Illuminate\Support\Str;

abstract class SingleInstanceWizardCommand extends SingleInstanceCommand
{
    use IsWizardCommand;

    public function executeSingle(array $data = []): array
    {
        if (! $step = $this->extractStepFromRequest()) {
            return $this->executeFirstStep($data);
        }

        $this->getWizardContext()->setCurrentStep($step);

        $methodName = 'executeStep'.Str::ucfirst(Str::camel($step));

        return method_exists($this, $methodName)
            ? $this->$methodName($data)
            : $this->executeStep($step, $data);
    }

    public function executeStep(string $step, array $data = []): array
    {
        // You can either implement this method and test $step (quick for small commands)
        // or leave this and implement for each step executeStepXXX
        // where XXX is the camel cased name of your step
        throw new SharpMethodNotImplementedException();
    }

    protected function initialSingleData(): array
    {
        if (! $step = $this->extractStepFromRequest()) {
            return $this->initialDataForFirstStep();
        }

        $methodName = 'initialDataForStep'.Str::ucfirst(Str::camel($step));

        return method_exists($this, $methodName)
            ? $this->$methodName()
            : $this->initialDataForStep($step);
    }

    protected function initialDataForFirstStep(): array
    {
        return [];
    }

    protected function initialDataForStep(string $step): array
    {
        return [];
    }

    abstract protected function executeFirstStep(array $data): array;

    abstract protected function buildFormFieldsForFirstStep(FieldsContainer $formFields): void;
}
