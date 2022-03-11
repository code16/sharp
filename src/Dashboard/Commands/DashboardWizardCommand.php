<?php

namespace Code16\Sharp\Dashboard\Commands;

use Code16\Sharp\EntityList\Commands\Wizards\IsEntityWizardCommand;
use Code16\Sharp\EntityList\Commands\Wizards\IsWizardCommand;
use Code16\Sharp\Utils\Fields\FieldsContainer;

abstract class DashboardWizardCommand extends DashboardCommand
{
    use IsWizardCommand, IsEntityWizardCommand;

    abstract protected function buildFormFieldsForFirstStep(FieldsContainer $formFields): void;
}
