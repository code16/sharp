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
     * @var bool
     */
    protected $shouldOptimize = false;

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
    public function setCropRatio(string $ratio = null, array $croppableFileTypes = null)
    {
        if($ratio) {
            $this->cropRatio = explode(":", $ratio);

            $this->croppableFileTypes = $croppableFileTypes
                ? $this->formatFileExtension($croppableFileTypes)
                : null;

        } else {
            $this->cropRatio = null;
            $this->croppableFileTypes = null;
        }

        return $this;
    }

    /**
     * @param bool $shouldOptimize
     * @return static
     */
    public function shouldOptimizeImage($shouldOptimizeImage = true)
    {
        $this->shouldOptimizeImage = $shouldOptimizeImage;

        return $this;
    }

    /**
     * @return bool
     */
    public function isShouldOptimize()
    {
        return $this->shouldOptimize;
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
