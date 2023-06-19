<?php

namespace App\Sharp\Profile\Commands;

use Code16\Sharp\Auth\TwoFactor\Commands\Deactivate2FaViaTotpSingleInstanceCommandTrait;
use Code16\Sharp\EntityList\Commands\SingleInstanceCommand;

class Deactivate2faCommand extends SingleInstanceCommand
{
    use Deactivate2FaViaTotpSingleInstanceCommandTrait;
}