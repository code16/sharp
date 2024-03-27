<?php

namespace Code16\Sharp\Show\Fields;

class SharpShowFileField extends SharpShowField
{
    const FIELD_TYPE = 'file';

    protected ?string $label = null;

    public static function make(string $key): SharpShowFileField
    {
        return new static($key, static::FIELD_TYPE);
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @deprecated Not needed, we deduce the disk from the value ("disk" attribute)
     */
    public function setStorageDisk(string $storageDisk): self
    {
        return $this;
    }

    /**
     * @deprecated Not needed, we deduce the base path the value ("path" attribute)
     */
    public function setStorageBasePath(string $storageBasePath): self
    {
        return $this;
    }

    public function toArray(): array
    {
        return parent::buildArray([
            'label' => $this->label,
        ]);
    }
}
