<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Formatters\UploadFormatter;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithUpload;

class SharpFormUploadField extends SharpFormField
{
    use SharpFormFieldWithUpload;

    const FIELD_TYPE = "upload";

    /**
     * @param string $key
     * @return static
     */
    public static function make(string $key)
    {
        return new static($key, static::FIELD_TYPE, new UploadFormatter);
    }

    /**
     * @return static
     */
    public function setFileFilterImages()
    {
        $this->setFileFilter([".jpg",".jpeg",".gif",".png"]);

        return $this;
    }

    /**
     * @return array
     */
    protected function validationRules()
    {
        return [
            "maxFileSize" => "numeric",
            "ratioX" => "integer|nullable",
            "ratioY" => "integer|nullable",
            "croppableFileTypes" => "array",
            "compactThumbnail" => "boolean",
            "shouldOptimizeImage" => "boolean"
        ];
    }

    /**
     * @return array
     * @throws \Code16\Sharp\Exceptions\Form\SharpFormFieldValidationException
     */
    public function toArray(): array
    {
        return parent::buildArray([
            "maxFileSize" => $this->maxFileSize,
            "fileFilter" => $this->fileFilter,
            "ratioX" => $this->cropRatio ? (int)$this->cropRatio[0] : null,
            "ratioY" => $this->cropRatio ? (int)$this->cropRatio[1] : null,
            "croppableFileTypes" => $this->croppableFileTypes,
            "compactThumbnail" => !!$this->compactThumbnail,
            "shouldOptimizeImage" => !!$this->shouldOptimizeImage
        ]);
    }
}
