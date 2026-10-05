<?php

namespace Code16\Sharp\Commands\Returns;

use Code16\Sharp\Enums\CommandAction;

class CommandRefreshReturn extends CommandReturn
{
    public function __construct(private readonly array $ids) {}

    protected function commandAction(): CommandAction
    {
        return CommandAction::Refresh;
    }

    public function getItems(): array
    {
        return $this->ids;
    }

    protected function additionalReturnData(): array
    {
        return [
            'items' => $this->ids,
        ];
    }
}
