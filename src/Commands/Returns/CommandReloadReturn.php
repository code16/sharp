<?php

namespace Code16\Sharp\Commands\Returns;

use Code16\Sharp\Enums\CommandAction;

class CommandReloadReturn extends CommandReturn
{
    private bool $showPageReload = false;

    public function withShowPageReload(bool $showPageReload = true): static
    {
        $this->showPageReload = $showPageReload;

        return $this;
    }

    protected function commandAction(): CommandAction
    {
        return CommandAction::Reload;
    }

    protected function additionalReturnData(): array
    {
        return [
            'showPageReload' => $this->showPageReload,
        ];
    }
}
