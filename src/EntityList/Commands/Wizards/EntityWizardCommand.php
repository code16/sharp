<?php

namespace Code16\Sharp\EntityList\Commands\Wizards;

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\Exceptions\SharpMethodNotImplementedException;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Illuminate\Support\Str;

abstract class EntityWizardCommand extends EntityCommand
{
    use IsWizardCommand;

    public function execute(array $data = []): array
    {
        $step = request()->query('command_step');

        if (! $step) {
            return $this->executeFirstStep($data);
        }

        $methodName = 'executeStep'.Str::ucfirst(Str::camel($step));

        return method_exists($this, $methodName)
            ? $this->$methodName($data)
            : $this->executeStep($step, $data);
    }

    abstract protected function executeFirstStep(array $data): array;

    public function executeStep(string $step, array $data = []): array
    {
        // You can either implement this method and test $step (quick for small commands)
        // or leave this and implement for each step executeStepXXX
        // where XXX is the camel cased name of your step
        throw new SharpMethodNotImplementedException();
    }

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $step = request()->query('command_step');

        if (! $step) {
            $this->buildFormFieldsForFirstStep($formFields);
        } else {
            $methodName = 'buildFormFieldsForStep'.Str::ucfirst(Str::camel($step));
            if (method_exists($this, $methodName)) {
                $this->$methodName($formFields);
            } else {
                $this->buildFormFieldsForStep($step, $formFields);
            }
        }
    }

    protected function buildFormFieldsForFirstStep(FieldsContainer $formFields): void
    {
        throw new SharpMethodNotImplementedException();
    }

    protected function buildFormFieldsForStep(string $step, FieldsContainer $formFields): void
    {
        // You can either implement this method and test $step (quick for small commands)
        // or leave this and implement for each step buildFormFieldsForStepXXX
        // where XXX is the camel cased name of your step
        throw new SharpMethodNotImplementedException();
    }

    public function buildFormLayout(FormLayoutColumn &$column): void
    {
        $step = request()->query('command_step');

        if (! $step) {
            $this->buildFormLayoutForFirstStep($column);
        } else {
            $methodName = 'buildFormLayoutForStep'.Str::ucfirst(Str::camel($step));
            if (method_exists($this, $methodName)) {
                $this->$methodName($column);
            } else {
                $this->buildFormLayoutForStep($step, $column);
            }
        }
    }

    protected function buildFormLayoutForFirstStep(FormLayoutColumn &$column): void
    {
    }

    protected function buildFormLayoutForStep(string $step, FormLayoutColumn &$column): void
    {
    }

    protected function initialData(): array
    {
        $step = request()->query('command_step');

        if (! $step) {
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
}
