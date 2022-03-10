<?php

namespace Code16\Sharp\EntityList\Commands\Wizards;

use Code16\Sharp\EntityList\Commands\InstanceCommand;

abstract class InstanceWizardCommand extends InstanceCommand
{
    use IsWizardCommand, IsInstanceWizardCommand;
}
