<?php

namespace Code16\Sharp\EntityList\Commands\Returns;

use Code16\Sharp\Enums\CommandAction;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CommandDownloadReturn extends CommandReturn implements Responsable
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

    public function getDiskName(): ?string
    {
        return $this->diskName;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function toResponse($request): StreamedResponse
    {
        return Storage::disk($this->diskName)->download($this->filePath, $this->fileName);
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
