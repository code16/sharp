<?php

namespace Code16\Sharp\EntityList\Commands\Returns;

use Code16\Sharp\Enums\CommandAction;

class CommandStreamDownloadReturn extends CommandReturn
{
    public function __construct(private readonly string $fileContent, private readonly string $fileName) {}

    protected function commandAction(): CommandAction
    {
        return CommandAction::StreamDownload;
    }

    protected function additionalReturnData(): array
    {
        return [
            'content' => $this->fileContent,
            'name' => $this->fileName,
        ];
    }
}
