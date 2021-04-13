<?php

namespace Code16\Sharp\Show\Fields;

class SharpShowFileField extends SharpShowField
{
    const FIELD_TYPE = "file";

    protected ?string $label = null;
    protected string $storageDisk = "local";
    protected string $storageBasePath = "data";

    public static function make(string $key): SharpShowFileField
    {
        return new static($key, static::FIELD_TYPE);
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function setStorageDisk(string $storageDisk): self
    {
        $this->storageDisk = $storageDisk;

        return $this;
    }

    public function setStorageBasePath(string $storageBasePath): self
    {
        $this->storageBasePath = $storageBasePath;

        return $this;
    }

    public function storageDisk(): string
    {
        return $this->storageDisk;
    }

    public function storageBasePath(): string
    {
        return $this->storageBasePath;
    }

    public function toArray(): array
    {
        return parent::buildArray([
            "label" => $this->label
        ]);
    }
}