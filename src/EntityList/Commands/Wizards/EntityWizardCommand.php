<?php

namespace Code16\Sharp\EntityList\Commands\Wizards;

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\Utils\Fields\FieldsContainer;

abstract class EntityWizardCommand extends EntityCommand
{
    use IsEntityWizardCommand;
    use IsWizardCommand;

    abstract protected function buildFormFieldsForFirstStep(FieldsContainer $formFields): void;
}
