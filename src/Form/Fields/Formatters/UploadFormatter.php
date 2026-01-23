<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Utils\FileUtil;
use Code16\Sharp\Utils\Uploads\SharpUploadManager;
use Illuminate\Filesystem\LocalFilesystemAdapter;
use Illuminate\Support\Facades\Storage;

class UploadFormatter extends SharpFieldFormatter implements FormatsAfterUpdate
{
    /**
     * @param  SharpFormUploadField  $field
     */
    public function toFront(SharpFormField $field, $value)
    {
        return $value;
    }

    /**
     * @param  SharpFormUploadField  $field
     */
    public function fromFront(SharpFormField $field, string $attribute, $value): ?array
    {
        if ($value['uploaded'] ?? false) {
            $uploadedFieldRelativePath = sprintf(
                '%s/%s',
                sharp()->config()->get('uploads.tmp_dir'),
                $value['name'],
            );

            return tap($this->normalizeFromFront($value, [
                'file_name' => $field->storageDisk() && ! sharp()->context()->isFormRefresh()
                    ? sprintf(
                        '%s/%s',
                        str($field->storageBasePath())->replace('{id}', $this->instanceId ?? '{id}'),
                        app(FileUtil::class)->findAvailableName(
                            $value['name'], $field->storageBasePath(), $field->storageDisk(),
                        )
                    )
                    : $uploadedFieldRelativePath,
                'size' => Storage::disk(sharp()->config()->get('uploads.tmp_disk'))
                    ->size($uploadedFieldRelativePath),
                'mime_type' => Storage::disk(sharp()->config()->get('uploads.tmp_disk'))
                    ->mimeType($uploadedFieldRelativePath),
                'disk' => $field->storageDisk() && ! sharp()->context()->isFormRefresh()
                    ? $field->storageDisk()
                    : sharp()->config()->get('uploads.tmp_disk'),
                'filters' => $field->isImageTransformOriginal()
                    ? null
                    : $value['filters'] ?? null,
            ]), function (&$formatted) use ($field, $value, $uploadedFieldRelativePath) {
                if ($field->storageDisk()) {
                    app(SharpUploadManager::class)->queueHandleUploadedFile(
                        uploadedFileName: $value['name'],
                        disk: $field->storageDisk(),
                        filePath: $formatted['file_name'],
                        shouldOptimizeImage: $field->isImageOptimize(),
                        shouldSanitizeSvg: $field->isImageSanitizeSvg(),
                        transformFilters: $field->isImageTransformOriginal()
                            ? ($value['filters'] ?? null)
                            : null,
                    );
                }

                if ($dimensions = $this->getImageDimensions($uploadedFieldRelativePath, $formatted['mime_type'])) {
                    $formatted['width'] = $dimensions['width'];
                    $formatted['height'] = $dimensions['height'];
                }
            });
        }

        if ($value['transformed'] ?? false) {
            // Transformation on an existing file
            return tap($this->normalizeFromFront($value), function ($formatted) use ($field) {
                if ($field->isImageTransformOriginal()) {
                    app(SharpUploadManager::class)->queueHandleTransformedFile(
                        disk: $field->storageDisk(),
                        filePath: $formatted['file_name'],
                        transformFilters: $formatted['filters'],
                    );
                }
            });
        }

        // No change was made
        return $this->normalizeFromFront($value);
    }

    public function afterUpdate(SharpFormField $field, string $attribute, mixed $value): ?array
    {
        if ($value['file_name'] ?? null) {
            $value['file_name'] = str($value['file_name'])
                ->replace('{id}', $this->instanceId)
                ->value();
        }

        return $value;
    }

    protected function normalizeFromFront(?array $value, ?array $formatted = null): ?array
    {
        if ($value === null) {
            return null;
        }

        return collect([
            'file_name' => $formatted['file_name'] ?? $value['path'] ?? null,
            'size' => $formatted['size'] ?? $value['size'] ?? null,
            'mime_type' => $formatted['mime_type'] ?? $value['mime_type'] ?? null,
            'disk' => $formatted['disk'] ?? $value['disk'] ?? null,
            'filters' => $formatted['filters'] ?? $value['filters'] ?? null,
        ])->whereNotNull()->toArray();
    }

    protected function getImageDimensions(string $tmpFilePath, string $mimeType): ?array
    {
        // image size only available if tmp is stored locally
        if (! Storage::disk(sharp()->config()->get('uploads.tmp_disk')) instanceof LocalFilesystemAdapter) {
            return null;
        }

        if (! str_starts_with($mimeType, 'image/')) {
            return null;
        }

        $realPath = Storage::disk(sharp()->config()->get('uploads.tmp_disk'))->path($tmpFilePath);

        if ($size = @getimagesize($realPath)) {
            return [
                'width' => $size[0],
                'height' => $size[1],
            ];
        }

        return null;
    }
}
