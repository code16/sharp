<?php

namespace Code16\Sharp\Http\Api;

use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class FilesController extends Controller
{
    public function show(string $entityKey, ?string $instanceId = null)
    {
        sharp_check_ability('view', $entityKey, $instanceId);

        $thumbnailWidth = request()->get('thumbnail_width', 400);
        $thumbnailHeight = request()->get('thumbnail_height', 400);

        return response()->json([
            'files' => collect(request()->get('files'))
                ->filter(function (array $file) {
                    return isset($file['disk'], $file['path']);
                })
                ->map(function (array $file) use ($thumbnailHeight, $thumbnailWidth) {
                    $disk = Storage::disk($file['disk']);
                    if (! $disk->exists($file['path'])) {
                        return null;
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
                                $model = new SharpUploadModel([
                                    'disk' => $file['disk'],
                                    'file_name' => $file['path'],
                                    'filters' => $file['filters'],
                                ]);

                                $file['thumbnail'] = $model->thumbnail($thumbnailWidth, $thumbnailHeight);
                            }
                        },
                    );
                })
                ->filter()
                ->values()
                ->toArray(),
        ]);
    }

    private function isMimetypeAnImage(string $mimetype): bool
    {
        return in_array($mimetype, ['image/jpeg', 'image/gif', 'image/png', 'image/bmp']);
    }
}
