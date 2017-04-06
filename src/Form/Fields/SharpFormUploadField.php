<?php

namespace Code16\Sharp\Form\Fields;

class SharpFormUploadField extends SharpFormField
{
    /**
     * @var string
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
     * @param string $key
     * @return static
     */
    public static function make(string $key)
    {
        return new static($key, 'upload');
    }

    /**
     * @param string $maxFileSize
     * @return $this
     */
    public function setMaxFileSize(string $maxFileSize)
    {
        $this->maxFileSize = $maxFileSize;

        return $this;
    }

    /**
     * @param string $fileFilter
     * @return $this
     */
    public function setFileFilter(string $fileFilter)
    {
        $this->fileFilter = $fileFilter;

        return $this;
    }

    /**
     * @return $this
     */
    public function setFileFilterImages()
    {
        $this->fileFilter = "jpg,jpeg,gif,png";

        return $this;
    }

    /**
     * @param string $thumbnail
     * @return $this
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
            "thumbnail" => $this->thumbnail
        ]);
    }
}