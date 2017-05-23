<?php

namespace Code16\Sharp\Form\Fields;

class SharpFormUploadField extends SharpFormField
{
    /**
     * @var float
     */
    protected $maxFileSize;

    /**
     * @var string
     */
    protected $fileFilter;

    /**
     * @var string
     */
    protected $thumbnail;

    /**
     * @var string
     */
    protected $cropRatio;

    /**
     * @param string $key
     * @return static
     */
    public static function make(string $key)
    {
        return new static($key, 'upload');
    }

    /**
     * @param float $maxFileSizeInMB
     * @return static
     */
    public function setMaxFileSize(float $maxFileSizeInMB)
    {
        $this->maxFileSize = $maxFileSizeInMB;

        return $this;
    }

    /**
     * @param string $fileFilter
     * @return static
     */
    public function setFileFilter(string $fileFilter)
    {
        $this->fileFilter = $fileFilter;

        return $this;
    }

    /**
     * @return static
     */
    public function setFileFilterImages()
    {
        $this->fileFilter = "jpg,jpeg,gif,png";

        return $this;
    }

    /**
     * @param string $ratio 16:9, 1:1, ...
     * @return static
     */
    public function setCropRatio(string $ratio)
    {
        $this->cropRatio = explode(":", $ratio);

        return $this;
    }

    /**
     * @param string $thumbnail
     * @return static
     */
    public function setThumbnail(string $thumbnail)
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }


    /**
     * @return array
     */
    protected function validationRules()
    {
        return [
            "thumbnail" => "regex:/[0-9]+x[0-9]+/",
            "maxFileSize" => "numeric",
            "ratioX" => "integer|nullable",
            "ratioY" => "integer|nullable",
        ];
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return parent::buildArray([
            "maxFileSize" => $this->maxFileSize,
            "fileFilter" => $this->fileFilter,
            "thumbnail" => $this->thumbnail,
            "ratioX" => $this->cropRatio ? $this->cropRatio[0] : null,
            "ratioY" => $this->cropRatio ? $this->cropRatio[1] : null,
        ]);
    }
}