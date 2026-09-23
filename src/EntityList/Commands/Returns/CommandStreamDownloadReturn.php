<?php

namespace Code16\Sharp\EntityList\Commands\Returns;

use Code16\Sharp\Enums\CommandAction;
use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CommandStreamDownloadReturn extends CommandReturn implements Responsable
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

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function toResponse($request): StreamedResponse
    {
        return response()->streamDownload(
            function () {
                echo $this->fileContent;
            },
            $this->fileName,
        );
    }

    protected function additionalReturnData(): array
    {
        return [
            'content' => $this->fileContent,
            'name' => $this->fileName,
        ];
    }
}
