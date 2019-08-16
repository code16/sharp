<?php

namespace Code16\Sharp\Show\Labels;

class SharpShowTextLabel extends SharpShowLabel
{
    const FIELD_TYPE = "text";

    /** @var string */
    protected $prependText = "";

    /**
     * @param string $key
     * @return static
     */
    public static function make(string $key)
    {
        return new static($key, static::FIELD_TYPE);
    }

    /**
     * @param string $prependText
     * @return $this
     */
    public function prependLabelWith(string $prependText)
    {
        $this->prependText = $prependText;

        return $this;
    }

    /**
     * Create the properties array for the field, using parent::buildArray()
     *
     * @return array
     */
    public function toArray(): array
    {
        return parent::buildArray([
            "prependLAbelWith" => $this->prependText,
        ]);
    }
}