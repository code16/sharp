<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Utils\FileUtil;
use Code16\Sharp\Utils\Uploads\SharpUploadManager;
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
                'file_name' => sprintf(
                    '%s/%s',
                    str($field->storageBasePath())->replace('{id}', $this->instanceId ?? '{id}'),
                    app(FileUtil::class)->findAvailableName(
                        $value['name'], $field->storageBasePath(), $field->storageDisk(),
                    )
                ),
                'size' => Storage::disk(sharp()->config()->get('uploads.tmp_disk'))
                    ->size($uploadedFieldRelativePath),
                'mime_type' => Storage::disk(sharp()->config()->get('uploads.tmp_disk'))
                    ->mimeType($uploadedFieldRelativePath),
                'disk' => $field->storageDisk(),
                'filters' => $field->isImageTransformOriginal()
                    ? null
                    : $value['filters'] ?? null,
            ]), function ($formatted) use ($field, $value) {
                app(SharpUploadManager::class)->queueHandleUploadedFile(
                    uploadedFileName: $value['name'],
                    disk: $field->storageDisk(),
                    filePath: $formatted['file_name'],
                    shouldOptimizeImage: $field->isImageOptimize(),
                    transformFilters: $field->isImageTransformOriginal()
                        ? ($value['filters'] ?? null)
                        : null,
                );
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
        if($value === null) {
            return null;
        }
        
        return collect([
            'file_name' => $formatted['file_name'] ?? $value['path'],
            'size' => $formatted['size'] ?? $value['size'] ?? null,
            'mime_type' => $formatted['mime_type'] ?? $value['mime_type'] ?? null,
            'disk' => $formatted['disk'] ?? $value['disk'],
            'filters' => $formatted['filters'] ?? $value['filters'] ?? null,
        ])->whereNotNull()->toArray();
    }
}
