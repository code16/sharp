<?php

namespace Code16\Sharp\Show\Fields;

use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithUpload;

class SharpShowFileField extends SharpShowField
{
    const FIELD_TYPE = "file";

    /** @var string */
    protected $label = null;

    /** @var string */
    protected $storageDisk = "local";
    
    /** @var string */
    protected $storageBasePath = "data";

    /**
     * @param string $key
     * @return static
     */
    public static function make(string $key): SharpShowFileField
    {
        return new static($key, static::FIELD_TYPE);
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel(string $label): self
    {
        $this->label = $label;

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
     * @inheritDoc
     */
    public function toArray(): array
    {
        return parent::buildArray([
            "label" => $this->label
        ]);
    }
}