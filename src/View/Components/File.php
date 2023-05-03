<?php

namespace Code16\Sharp\View\Components;

use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;
use Code16\Sharp\Form\Eloquent\Uploads\Traits\UsesSharpUploadModel;
use Code16\Sharp\View\Utils\ContentComponent;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class File extends ContentComponent
{
    use UsesSharpUploadModel;

    public ?SharpUploadModel $fileModel = null;
    public ?FilesystemAdapter $disk = null;
    public bool $exists = false;

    public function __construct(
        ?string $path = null,
        ?string $disk = null,
        public ?string $name = null,
    ) {
        if ($path) {
            $this->fileModel = static::getUploadModelClass()::make([
                'disk' => $disk,
                'file_name' => $path,
            ]);
            $this->disk = Storage::disk($this->fileModel->disk);
            $this->exists = $this->disk->exists($this->fileModel->file_name);
            $this->name ??= basename($this->fileModel->file_name);
        }
    }

    public function render(): View
    {
        if (! $this->fileModel) {
            return view('sharp::components.file-error', [
                'message' => "<x-sharp-file name=\"$this->name\"> has no path defined. An error must have occured during the form submission.",
            ]);
        }

        return view('sharp::components.file');
    }
}
