<?php

namespace Code16\Sharp\EntityList\Commands\Returns;

use Code16\Sharp\Enums\CommandAction;

class CommandDownloadReturn extends CommandReturn
{
    public function __construct(
        private readonly string $filePath,
        private readonly ?string $fileName = null,
        private readonly ?string $diskName = null,
    ) {}

    protected function commandAction(): CommandAction
    {
        return CommandAction::Download;
    }

    protected function additionalReturnData(): array
    {
        return [
            'file' => $this->filePath,
            'disk' => $this->diskName,
            'name' => $this->fileName,
        ];
    }
}
