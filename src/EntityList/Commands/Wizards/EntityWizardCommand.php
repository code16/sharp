<?php

namespace Code16\Sharp\EntityList\Commands\Wizards;

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\Utils\Fields\FieldsContainer;

abstract class EntityWizardCommand extends EntityCommand
{
    use IsWizardCommand, IsEntityWizardCommand;

    final public function requiresSelect(): bool
    {
        return false;
    }

    final public function allowsSelect(): bool
    {
        return false;
    }

    abstract protected function buildFormFieldsForFirstStep(FieldsContainer $formFields): void;
}
