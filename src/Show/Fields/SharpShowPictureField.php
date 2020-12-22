<?php

namespace Code16\Sharp\Show\Fields;

class SharpShowPictureField extends SharpShowField
{
    const FIELD_TYPE = "picture";

    public static function make(string $key): SharpShowPictureField
    {
        return new static($key, static::FIELD_TYPE);
    }

    /**
     * Create the properties array for the field, using parent::buildArray()
     */
    public function toArray(): array
    {
        return parent::buildArray([]);
    }
}