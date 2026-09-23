<?php

namespace Code16\Sharp\Commands\Returns;

use Code16\Sharp\Enums\CommandAction;

class CommandViewReturn extends CommandReturn
{
    public function __construct(private readonly string $html) {}

    protected function commandAction(): CommandAction
    {
        return CommandAction::View;
    }

    protected function additionalReturnData(): array
    {
        return [
            'html' => $this->html,
        ];
    }
}
