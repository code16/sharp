<?php

namespace Code16\Sharp\EntityList\Commands\Returns;

use Code16\Sharp\Enums\CommandAction;

class CommandRefreshReturn extends CommandReturn
{
    public function __construct(private readonly array $ids) {}

    protected function commandAction(): CommandAction
    {
        return CommandAction::Refresh;
    }

    protected function additionalReturnData(): array
    {
        return [
            'items' => $this->ids,
        ];
    }
}
