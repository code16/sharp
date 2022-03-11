<?php

namespace Code16\Sharp\Dashboard\Commands;

use Code16\Sharp\EntityList\Commands\Wizards\IsEntityWizardCommand;
use Code16\Sharp\EntityList\Commands\Wizards\IsWizardCommand;

abstract class DashboardWizardCommand extends DashboardCommand
{
    use IsWizardCommand, IsEntityWizardCommand;
}
