<?php

namespace Code16\Sharp\EntityList\Commands\Wizards;

use Code16\Sharp\Enums\CommandAction;
use Code16\Sharp\Exceptions\SharpMethodNotImplementedException;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Illuminate\Support\Str;

trait IsWizardCommand
{
    protected ?WizardCommandContext $wizardCommandContext = null;
    private string $key;
    private array $formsBuilt = [];

    protected function getWizardContext(): WizardCommandContext
    {
        if (! $this->wizardCommandContext) {
            $this->wizardCommandContext = session()->get(sprintf('CWC.%s.%s', get_class($this), $this->getKey()));
            if (! $this->wizardCommandContext) {
                $this->wizardCommandContext = new WizardCommandContext();
            }
        }

        return $this->wizardCommandContext;
    }

    protected function toStep(string $step): array
    {
        if ($this->wizardCommandContext) {
            session()->put(sprintf('CWC.%s.%s', get_class($this), $this->getKey()), $this->wizardCommandContext);
        }

        return [
            'action' => CommandAction::Step->value,
            'step' => "{$step}:{$this->getKey()}",
        ];
    }

    protected function extractStepFromRequest(): ?string
    {
        if ($step = request()->get('command_step')) {
            [$step, $this->key] = explode(':', $step);

            return $step;
        }

        return null;
    }

    protected function isStep(string $step): bool
    {
        return $this->extractStepFromRequest() == $step;
    }

    protected function getKey(): string
    {
        if (! isset($this->key)) {
            $this->key = Str::random(5);
        }

        return $this->key;
    }

    public function buildFormFields(FieldsContainer $formFields): void
    {
        if (! $step = $this->extractStepFromRequest()) {
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

    protected function buildFormFieldsForStep(string $step, FieldsContainer $formFields): void
    {
        // You can either implement this method and test $step (quick for small commands)
        // or leave this and implement for each step buildFormFieldsForStepXXX
        // where XXX is the camel cased name of your step
        throw new SharpMethodNotImplementedException();
    }

    public function buildFormLayout(FormLayoutColumn &$column): void
    {
        if (! $step = $this->extractStepFromRequest()) {
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

    protected function buildFormLayoutForFirstStep(FormLayoutColumn &$column): void {}

    protected function buildFormLayoutForStep(string $step, FormLayoutColumn &$column): void {}

    /**
     * Override the default checkFormIsBuilt() in Wizard case, to check the form
     * given the current step.
     */
    protected function checkFormIsBuilt(): void
    {
        $step = $this->extractStepFromRequest() ?: '##first_step##';
        if (! ($this->formsBuilt[$step] ?? false)) {
            $this->fieldsContainer = null;
            $this->buildFormFields($this->fieldsContainer());
            $this->formsBuilt[$step] = count($this->fieldsContainer()->getFields()) > 0;
        }
    }
}
