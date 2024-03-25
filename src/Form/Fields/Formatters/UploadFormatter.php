<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Http\Context\CurrentSharpRequest;
use Code16\Sharp\Http\Jobs\HandleTransformedFileJob;
use Code16\Sharp\Http\Jobs\HandleUploadedFileJob;
use Code16\Sharp\Utils\FileUtil;
use Illuminate\Support\Facades\Storage;

class UploadFormatter extends SharpFieldFormatter implements FormatsAfterUpdate
{
    private bool $alwaysReturnFullObject = false;

    public function setAlwaysReturnFullObject(?bool $returnFullObject = true): self
    {
        $this->alwaysReturnFullObject = $returnFullObject;

        return $this;
    }

    /**
     * @param SharpFormUploadField $field
     */
    public function toFront(SharpFormField $field, $value)
    {
        return $value;
    }

    /**
     * @param SharpFormUploadField $field
     */
    public function fromFront(SharpFormField $field, string $attribute, $value): ?array
    {
        if ($value['uploaded'] ?? false) {
            $uploadedFieldRelativePath = sprintf(
                '%s/%s',
                config('sharp.uploads.tmp_dir', 'tmp'),
                $value['name'],
            );

            return tap($this->maybeFullObject($value, [
                'file_name' => sprintf(
                    '%s/%s',
                    str($field->storageBasePath())->replace('{id}', $this->instanceId ?? '{id}'),
                    app(FileUtil::class)->findAvailableName(
                        $value['name'], $field->storageBasePath(), $field->storageDisk(),
                    )
                ),
                'size' => Storage::disk(config('sharp.uploads.tmp_disk', 'local'))
                    ->size($uploadedFieldRelativePath),
                'mime_type' => Storage::disk(config('sharp.uploads.tmp_disk', 'local'))
                    ->mimeType($uploadedFieldRelativePath),
                'disk' => $field->storageDisk(),
                'filters' => $field->isImageTransformOriginal()
                    ? null
                    : $value['filters'] ?? null,
            ]), function ($formatted) use ($field, $value) {
                app(CurrentSharpRequest::class)->queueAfterFormUpdate(
                    new HandleUploadedFileJob(
                        uploadedFileName: $value['name'],
                        disk: $field->storageDisk(),
                        filePath: $formatted['file_name'],
                        shouldOptimizeImage: $field->isImageOptimize(),
                        transformFilters: $field->isImageTransformOriginal()
                            ? ($value['filters'] ?? null)
                            : null,
                    )
                );
            });
        }
        
        
        if ($value['transformed'] ?? false) {
            // Transformation on an existing file
            return tap($this->maybeFullObject($value, [
                'file_name' => $value['path'],
                'disk' => $value['disk'],
                'size' => $value['size'],
                'filters' => $value['filters'] ?? null,
            ]), function ($formatted) use ($field, $value) {
                if($field->isImageTransformOriginal()) {
                    app(CurrentSharpRequest::class)->queueAfterFormUpdate(
                        new HandleTransformedFileJob(
                            disk: $field->storageDisk(),
                            filePath: $formatted['file_name'],
                            transformFilters: $formatted['filters'],
                        )
                    );
                }
            });
        }

        // No change was made
        return $this->maybeFullObject($value, $value === null ? null : []);
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
    
    protected function maybeFullObject(?array $value, ?array $formatted): ?array
    {
        if ($this->alwaysReturnFullObject) {
            return $value === null ? null :
                 collect([
                    'file_name' => $formatted['file_name'] ?? $value['path'],
                    'size' => $formatted['size'] ?? $value['size'] ?? null,
                    'mime_type' => $formatted['mime_type'] ?? $value['mime_type'] ?? null,
                    'disk' => $formatted['disk'] ?? $value['disk'],
                    'filters' => $formatted['filters'] ?? $value['filters'] ?? null,
                ])->whereNotNull()->toArray();
        }
        
        return $formatted;
    }
}
