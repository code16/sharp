<?php

namespace Code16\Sharp\EntityList\Commands\Returns;

use Code16\Sharp\Enums\CommandAction;

class CommandLinkReturn extends CommandReturn
{
    private bool $openInNewTab = false;

    public function __construct(private readonly string $link) {}

    public function inNewTab(bool $openInNewTab = true): self
    {
        $this->openInNewTab = $openInNewTab;

        return $this;
    }

    protected function commandAction(): CommandAction
    {
        return CommandAction::Link;
    }

    protected function additionalReturnData(): array
    {
        return [
            'link' => $this->link,
            'openInNewTab' => $this->openInNewTab,
        ];
    }
}
