<?php

namespace Code16\Sharp\Data\Form\Fields;

use Code16\Sharp\Data\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

/**
 * Only for typing.
 */
#[LiteralTypeScriptType('{ [key:string]: any } & {
    _html: string|null,
    _htmlResult: string|null
}')]
final class FormAutocompleteItemData extends Data
{
}
