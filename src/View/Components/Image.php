<?php

namespace Code16\Sharp\View\Components;

use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;
use Code16\Sharp\View\Utils\ContentComponent;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class Image extends ContentComponent
{
    public SharpUploadModel $fileModel;
    public FilesystemAdapter $disk;
    public bool $exists;

    public function __construct(
        string $path,
        ?string $disk = null,
        public ?string $name = null,
        public ?string $filterCrop = null,
        public ?string $filterRotate = null,
        public ?int $thumbnailWidth = null,
        public ?int $thumbnailHeight = null,
        public ?array $filters = [],
    ) {
        $this->fileModel = new SharpUploadModel([
            'disk' => $disk,
            'file_name' => $path,
            'filters' => $this->getTransformationFilters(),
        ]);
        $this->disk = Storage::disk($this->fileModel->disk);
        $this->exists = $this->disk->exists($this->fileModel->file_name);

        if (! $this->thumbnailWidth && ! $this->thumbnailHeight) {
            $this->thumbnailWidth = 500;
        }
    }

    protected function getTransformationFilters(): array
    {
        $filters = [];

        if ($cropData = $this->filterCrop) {
            $cropValues = explode(',', $cropData);
            $filters['crop'] = [
                'x' => $cropValues[0],
                'y' => $cropValues[1],
                'width' => $cropValues[2],
                'height' => $cropValues[3],
            ];
        }

        if ($rotateAngle = $this->filterRotate) {
            $filters['rotate'] = [
                'angle' => $rotateAngle,
            ];
        }

        return $filters;
    }

    public function render(): View
    {
        return view('sharp::components.image');
    }
}
