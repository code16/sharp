<?php

namespace Code16\Sharp\View\Components;

use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Component;
use Illuminate\View\View;


class Media extends Component
{
    public SharpUploadModel $fileModel;
    public FilesystemAdapter $disk;
    public bool $exists;
    public bool $isImage;
    public ?int $width;
    public ?int $height;
    public array $filters = [];
    
    public function __construct(
        ?Content $content,
        ?string $disk = null,
        ?string $path = null,
        ?string $name = null,
    ) {
        $this->fileModel = new SharpUploadModel([
            'disk' => $disk,
            'file_name' => $path,
        ]);
        $this->disk = Storage::disk($disk);
        $this->exists = $this->disk->exists($this->fileModel->file_name);
        $this->isImage = $this->isImage();
        $this->width = $content->imageWidth;
        $this->height = $content->imageHeight;
        
        if(!$this->width && !$this->height) {
            $this->width = 500;
        }
    }
    
    protected function isImage(): bool
    {
        if(!$this->exists) {
            return false;
        }
        $mimeType = $this->disk->mimeType($this->fileModel->file_name);
        return in_array($mimeType, ['image/jpeg','image/gif','image/png','image/bmp']);
    }
    
    public function render(): View
    {
        return view('sharp::components.media');
    }
}
