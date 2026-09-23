<?php

namespace Code16\Sharp\Commands\Returns;

use Code16\Sharp\Enums\CommandAction;

class CommandReloadReturn extends CommandReturn
{
    protected function commandAction(): CommandAction
    {
        return CommandAction::Reload;
    }
}
