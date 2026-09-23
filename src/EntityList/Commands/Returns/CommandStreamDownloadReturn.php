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

    public function getFileContent(): string
    {
        return $this->fileContent;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    protected function additionalReturnData(): array
    {
        return [
            'content' => $this->fileContent,
            'name' => $this->fileName,
        ];
    }
}
