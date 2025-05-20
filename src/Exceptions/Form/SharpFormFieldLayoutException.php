<?php

namespace Code16\Sharp\Exceptions\Form;

use Code16\Sharp\Exceptions\SharpException;

class SharpFormFieldLayoutException extends SharpException
{
    public static function undeclaredField(string $key): self
    {
        return new static('Field defined in layout as ['.$key.'] was not declared.');
    }

    public static function regularFieldDeclaredAsListField($key): self
    {
        return new static('Field ['.$key.'] is defined as a List in the layout but is not one.');
    }

    public static function listFieldDeclaredAsRegularField($key): self
    {
        return new static('Field ['.$key.'] is not defined as a List in the layout and it should be.');
    }
}
