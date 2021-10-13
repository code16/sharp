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
    
    public function __construct(
        string $path,
        ?string $disk = null,
        public ?string $name = null,
        public ?string $filterCrop = null,
        public ?string $filterRotate = null,
        public ?int $width = null,
        public ?int $height = null,
        public ?array $filters = [],
    ) {
        $this->fileModel = new SharpUploadModel([
            'disk' => $disk,
            'file_name' => $path,
            'filters' => $this->getTransformationFilters(),
        ]);
        $this->disk = Storage::disk($this->fileModel->disk);
        $this->exists = $this->disk->exists($this->fileModel->file_name);
        $this->isImage = $this->isImage();
        
        if(!$this->width && !$this->height) {
            $this->width = 500;
        }
    }
    
    protected function getTransformationFilters(): array
    {
        $filters = [];
        
        if($cropData = $this->filterCrop) {
            $cropValues = explode(",", $cropData);
            $filters["crop"] = [
                "x" => $cropValues[0],
                "y" => $cropValues[1],
                "width" => $cropValues[2],
                "height" => $cropValues[3],
            ];
        }
        
        if($rotateAngle = $this->filterRotate) {
            $filters["rotate"] = [
                'angle' => $rotateAngle,
            ];
        }
        
        return $filters;
    }
    
    protected function isImage(): bool
    {
        if(!$this->exists) {
            return false;
        }
        $mimeType = $this->disk->mimeType($this->fileModel->file_name);
        return in_array($mimeType, [
            'image/jpeg',
            'image/gif',
            'image/png',
            'image/bmp',
        ]);
    }
    
    public function render()
    {
        return view('sharp::components.media');
    }
}
