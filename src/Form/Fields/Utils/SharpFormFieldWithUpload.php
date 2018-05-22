<?php

namespace Code16\Sharp\Form\Fields\Utils;

trait SharpFormFieldWithUpload
{
    /**
     * @var float
     */
    protected $maxFileSize;

    /**
     * @var string
     */
    protected $cropRatio;

    /**
     * @var array
     */
    protected $croppableFileTypes;

    /**
     * @var string
     */
    protected $storageDisk = "local";

    /**
     * @var string
     */
    protected $storageBasePath = "data";

    /**
     * @var bool
     */
    protected $compactThumbnail = false;

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
     * @param string $ratio 16:9, 1:1, ...
     * @param array|null $croppableFileTypes
     * @return static
     */
    public function setCropRatio(string $ratio, array $croppableFileTypes = null)
    {
        $this->cropRatio = explode(":", $ratio);
        $this->croppableFileTypes = $croppableFileTypes;

        return $this;
    }

    /**
     * @param bool $compactThumbnail
     * @return static
     */
    public function setCompactThumbnail($compactThumbnail = true)
    {
        $this->compactThumbnail = $compactThumbnail;

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
    public function storageDisk()
    {
        return $this->storageDisk;
    }

    /**
     * @return string
     */
    public function storageBasePath()
    {
        return $this->storageBasePath;
    }

    /**
     * @return string
     */
    public function cropRatio()
    {
        return $this->cropRatio;
    }
}