<?php

namespace Code16\Sharp\Show\Fields;

class SharpShowListField extends SharpShowField
{
    const FIELD_TYPE = "list";

    /** @var string */
    protected $label = null;

    /**
     * @param string $key
     * @return static
     */
    public static function make(string $key)
    {
        return new static($key, static::FIELD_TYPE);
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel(string $label)
    {
        $this->label = $label;

        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return parent::buildArray([
            "label" => $this->label,
        ]);
    }
}