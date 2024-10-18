<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Exceptions\SharpInvalidConfigException;
use Code16\Sharp\Form\Fields\Utils\SharpFormAutocompleteCommonField;

abstract class SharpFormAutocompleteField
{
    use SharpFormAutocompleteCommonField;

    /**
     * @deprecated use SharpFormAutocompleteLocalField::make() or SharpFormAutocompleteRemoteField::make() instead
     */
    public static function make(string $key, string $mode): SharpFormAutocompleteLocalField|SharpFormAutocompleteRemoteField
    {
        return match ($mode) {
            'local' => SharpFormAutocompleteLocalField::make($key),
            'remote' => SharpFormAutocompleteRemoteField::make($key),
            default => throw new SharpInvalidConfigException('Invalid autocomplete mode:' . $mode),
        };
    }
}
