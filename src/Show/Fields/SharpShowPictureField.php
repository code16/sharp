<?php

namespace Code16\Sharp\Show\Fields;

class SharpShowPictureField extends SharpShowField
{
    const FIELD_TYPE = "picture";

    /**
     * @param string $key
     * @return static
     */
    public static function make(string $key)
    {
        return new static($key, static::FIELD_TYPE);
    }

    /**
     * Create the properties array for the field, using parent::buildArray()
     *
     * @return array
     * @throws \Code16\Sharp\Exceptions\Show\SharpShowFieldValidationException
     */
    public function toArray(): array
    {
        return parent::buildArray([]);
    }
}