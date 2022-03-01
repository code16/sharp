<?php

namespace Code16\Sharp\View\Components;

use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;
use Code16\Sharp\View\Utils\ContentComponent;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class File extends ContentComponent
{
    public SharpUploadModel $fileModel;
    public FilesystemAdapter $disk;
    public bool $exists;

    public function __construct(
        string $path,
        ?string $disk = null,
        public ?string $name = null,
    ) {
        $this->fileModel = new SharpUploadModel([
            'disk' => $disk,
            'file_name' => $path,
        ]);
        $this->disk = Storage::disk($this->fileModel->disk);
        $this->exists = $this->disk->exists($this->fileModel->file_name);
    }

    public function render(): View
    {
        return view('sharp::components.file');
    }
}
