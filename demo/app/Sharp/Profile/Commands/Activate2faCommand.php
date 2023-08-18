<?php

namespace App\Sharp\Profile\Commands;

use Code16\Sharp\Auth\TwoFactor\Commands\Activate2faViaTotpWizardCommandTrait;
use Code16\Sharp\EntityList\Commands\Wizards\SingleInstanceWizardCommand;

class Activate2faCommand extends SingleInstanceWizardCommand
{
    use Activate2faViaTotpWizardCommandTrait;
}
