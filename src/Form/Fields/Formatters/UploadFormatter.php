<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\Utils\IsUploadField;
use Code16\Sharp\Utils\FileUtil;
use Illuminate\Support\Facades\Storage;

class UploadFormatter extends SharpFieldFormatter
{
    private bool $alwaysReturnFullObject = false;
    const ID_PLACEHOLDER = '__id_placeholder__';

    public function setAlwaysReturnFullObject(?bool $returnFullObject = true): self
    {
        $this->alwaysReturnFullObject = $returnFullObject;

        return $this;
    }

    public function toFront(SharpFormField $field, $value)
    {
        return $value;
    }

    /**
     * @param IsUploadField $field
     */
    public function fromFront(SharpFormField $field, string $attribute, $value): ?array
    {
        if ($value['uploaded'] ?? false) {
            $uploadedFieldRelativePath = sprintf(
                '%s/%s',
                config('sharp.uploads.tmp_dir', 'tmp'),
                $value['name'],
            );

            return [
                'file_name' => sprintf(
                    '%s/%s',
                    str($field->storageBasePath())->replace('{id}', self::ID_PLACEHOLDER),
                    app(FileUtil::class)->findAvailableName(
                        $value['name'], $field->storageBasePath(), $field->storageDisk(),
                    )
                ),
                'size' => Storage::disk(config('sharp.uploads.tmp_disk', 'local'))
                    ->size($uploadedFieldRelativePath),
                'mime_type' => Storage::disk(config('sharp.uploads.tmp_disk', 'local'))
                    ->mimeType($uploadedFieldRelativePath),
                'disk' => $field->storageDisk(),
                'filters' => $field->isTransformOriginal()
                    ? null
                    : $value['filters'] ?? null,
            ];
        }

        if ($value['transformed'] ?? false) {
            // Transformation on an existing file
            return $this->alwaysReturnFullObject
                ? $value
                : [
                    'file_name' => $value['path'],
                    'size' => $value['size'],
                    'disk' => $value['disk'],
                    'filters' => $value['filters'] ?? null,
                ];
        }

        // No change was made
        return $this->alwaysReturnFullObject
            ? $value
            : ($value === null ? null : []);
    }

    public function afterUpdate(SharpFormField $field, string $attribute, $value)
    {
        if ($value['file_name'] ?? null) {
            $value['file_name'] = str($value['file_name'])
                ->replace(self::ID_PLACEHOLDER, $this->instanceId)
                ->value();
        }

        return $value;
    }
}
