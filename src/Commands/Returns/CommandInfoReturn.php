<?php

namespace Code16\Sharp\Commands\Returns;

use Code16\Sharp\Enums\CommandAction;

class CommandInfoReturn extends CommandReturn
{
    private bool $reload = false;
    private bool $showPageReload = false;

    public function __construct(private readonly string $message) {}

    public function withReload(bool $reload = true): self
    {
        $this->reload = $reload;

        return $this;
    }

    public function withShowPageReload(bool $showPageReload = true): self
    {
        $this->showPageReload = $showPageReload;

        return $this;
    }

    protected function commandAction(): CommandAction
    {
        return CommandAction::Info;
    }

    protected function additionalReturnData(): array
    {
        return [
            'message' => $this->message,
            'reload' => $this->reload || $this->showPageReload,
            'showPageReload' => $this->showPageReload,
        ];
    }
}
