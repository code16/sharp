<?php

namespace Code16\Sharp\EntityList\Commands\Returns;

use Code16\Sharp\Enums\CommandAction;

class CommandLinkReturn extends CommandReturn
{
    public function __construct(private readonly string $link, private readonly bool $openInNewTab = false) {}

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
