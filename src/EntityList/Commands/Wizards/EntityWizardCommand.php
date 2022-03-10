<?php

namespace Code16\Sharp\EntityList\Commands\Wizards;

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\Exceptions\SharpMethodNotImplementedException;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Illuminate\Support\Str;

abstract class EntityWizardCommand extends EntityCommand
{
    use IsWizardCommand, IsEntityWizardCommand;
}
