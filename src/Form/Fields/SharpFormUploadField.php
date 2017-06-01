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
    protected $fileFilter = null;

    /**
     * @var string
     */
    protected $cropRatio;

    /**
     * @var string
     */
    protected $storageDisk = "local";

    /**
     * @var string
     */
    protected $storageBasePath = "data";

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
     * @param string|array $fileFilter
     * @return static
     */
    public function setFileFilter($fileFilter)
    {
        $this->fileFilter = $this->formatFileFilter($fileFilter);

        return $this;
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
     * @param string $ratio 16:9, 1:1, ...
     * @return static
     */
    public function setCropRatio(string $ratio)
    {
        $this->cropRatio = explode(":", $ratio);

        return $this;
    }

    /**
     * @param string $storageDisk
     * @return static
     */
    public function setStorageDisk(string $storageDisk)
    {
        $this->storageDisk = $storageDisk;

        return $this;
    }

    /**
     * @param string $storageBasePath
     * @return static
     */
    public function setStorageBasePath(string $storageBasePath)
    {
        $this->storageBasePath = $storageBasePath;

        return $this;
    }

    /**
     * @return string
     */
    public function storageDisk(): string
    {
        return $this->storageDisk;
    }

    /**
     * @return string
     */
    public function storageBasePath(): string
    {
        return $this->storageBasePath;
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
            "ratioX" => $this->cropRatio ? (int)$this->cropRatio[0] : null,
            "ratioY" => $this->cropRatio ? (int)$this->cropRatio[1] : null,
        ]);
    }

    private function formatFileFilter($fileFilter)
    {
        if(!is_array($fileFilter)) {
            $fileFilter = explode(",", $fileFilter);
        }

        return collect($fileFilter)->map(function($filter) {
            $filter = trim($filter);
            if(substr($filter, 0, 1) != ".") {
                $filter = ".$filter";
            }
            return $filter;

        })->all();
    }
}