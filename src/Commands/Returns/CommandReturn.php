<?php

namespace Code16\Sharp\Commands\Returns;

use Code16\Sharp\Enums\CommandAction;
use Illuminate\Contracts\Support\Arrayable;

abstract class CommandReturn implements Arrayable
{
    public function toArray(): array
    {
        return [
            'action' => $this->commandAction()->value,
            ...$this->additionalReturnData(),
        ];
    }

    protected function additionalReturnData(): array
    {
        return [];
    }

    abstract protected function commandAction(): CommandAction;
}
