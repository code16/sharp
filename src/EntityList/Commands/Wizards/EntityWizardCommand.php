<?php

namespace Code16\Sharp\EntityList\Commands\Wizards;

use Code16\Sharp\EntityList\Commands\EntityCommand;

abstract class EntityWizardCommand extends EntityCommand
{
    use IsWizardCommand, IsEntityWizardCommand;
}
