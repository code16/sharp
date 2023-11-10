<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Formatters\UploadFormatter;
use Code16\Sharp\Form\Fields\Utils\IsUploadField;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithUpload;

class SharpFormUploadField extends SharpFormField implements IsUploadField
{
    use SharpFormFieldWithUpload;

    const FIELD_TYPE = 'upload';

    public static function make(string $key): self
    {
        return new static($key, static::FIELD_TYPE, app(UploadFormatter::class));
    }

    protected function validationRules(): array
    {
        return [
            'maxFileSize' => 'numeric',
            'ratioX' => 'integer|nullable',
            'ratioY' => 'integer|nullable',
            'transformable' => 'boolean',
            'transformableFileTypes' => 'array',
            'transformKeepOriginal' => 'boolean',
            'compactThumbnail' => 'boolean',
        ];
    }

    public function toArray(): array
    {
        return parent::buildArray([
            'maxFileSize' => $this->maxFileSize,
            'fileFilter' => $this->fileFilter,
            'ratioX' => $this->cropRatio ? (int) $this->cropRatio[0] : null,
            'ratioY' => $this->cropRatio ? (int) $this->cropRatio[1] : null,
            'transformable' => $this->transformable,
            'transformableFileTypes' => $this->transformableFileTypes,
            'transformKeepOriginal' => $this->transformKeepOriginal(),
            'compactThumbnail' => (bool) $this->compactThumbnail,
        ]);
    }
}
