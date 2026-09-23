<?php

namespace Code16\Sharp\EntityList\Commands\Returns;

use Code16\Sharp\Enums\CommandAction;

class CommandStepReturn extends CommandReturn
{
    public function __construct(private readonly string $step) {}

    protected function commandAction(): CommandAction
    {
        return CommandAction::Step;
    }

    protected function additionalReturnData(): array
    {
        return [
            'step' => $this->step,
        ];
    }
}
