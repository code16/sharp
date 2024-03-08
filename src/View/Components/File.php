<?php

namespace Code16\Sharp\View\Components;

use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;
use Code16\Sharp\Form\Eloquent\Uploads\Traits\UsesSharpUploadModel;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Component;
use Illuminate\View\View;

class File extends Component
{
    use UsesSharpUploadModel;

    public array $file;
    public ?string $name = null;
    public ?SharpUploadModel $fileModel = null;
    public ?Filesystem $disk = null;
    public bool $exists = false;

    public function __construct(
        string $file,
        public ?string $legend = null
    ) {
        if ($this->file = json_decode(htmlspecialchars_decode($file), true)) {
            $this->fileModel = static::getUploadModelClass()::make([
                'file_name' => $this->file['path'],
                'disk' => $this->file['disk'] ?? null,
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
