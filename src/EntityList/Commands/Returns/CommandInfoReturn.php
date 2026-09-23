<?php

namespace Code16\Sharp\EntityList\Commands\Returns;

use Code16\Sharp\Enums\CommandAction;

class CommandInfoReturn extends CommandReturn
{
    public function __construct(private readonly string $message, private readonly bool $reload) {}

    protected function commandAction(): CommandAction
    {
        return CommandAction::Info;
    }

    protected function additionalReturnData(): array
    {
        return [
            'message' => $this->message,
            'reload' => $this->reload,
        ];
    }
}
