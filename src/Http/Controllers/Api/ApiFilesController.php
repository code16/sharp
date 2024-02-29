<?php

namespace Code16\Sharp\Http\Controllers\Api;

use Code16\Sharp\Form\Eloquent\Uploads\Traits\UsesSharpUploadModel;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class ApiFilesController extends Controller
{
    use UsesSharpUploadModel;

    public function show(string $entityKey, ?string $instanceId = null)
    {
        sharp_check_ability('view', $entityKey, $instanceId);

        $thumbnailWidth = request()->get('thumbnail_width', 200);
        $thumbnailHeight = request()->get('thumbnail_height', 200);

        return response()->json([
            'files' => collect(request()->get('files'))
                ->map(function (array $file) use ($thumbnailHeight, $thumbnailWidth) {
                    if(!isset($file['disk'], $file['path'])) {
                        return [...$file, 'not_found' => true];
                    }
                    
                    $disk = Storage::disk($file['disk']);
                    if (! $disk->exists($file['path'])) {
                        return [...$file, 'not_found' => true];
                    }

                    return tap(
                        [
                            'name' => basename($file['path']),
                            'path' => $file['path'],
                            'disk' => $file['disk'],
                            'size' => $disk->size($file['path']),
                            'filters' => $file['filters'] ?? null,
                        ],
                        function (array &$file) use ($disk, $thumbnailHeight, $thumbnailWidth) {
                            if ($this->isMimetypeAnImage($disk->mimeType($file['path']))) {
                                $model = static::getUploadModelClass()::make([
                                    'disk' => $file['disk'],
                                    'file_name' => $file['path'],
                                    'filters' => $file['filters'],
                                ]);

                                $file['thumbnail'] = $model->thumbnail($thumbnailWidth, $thumbnailHeight);
                            }
                        },
                    );
                })
                ->values()
                ->toArray(),
        ]);
    }

    private function isMimetypeAnImage(string $mimetype): bool
    {
        return in_array($mimetype, ['image/jpeg', 'image/gif', 'image/png', 'image/bmp']);
    }
}
