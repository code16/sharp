<?php

namespace Code16\Sharp\Data\Form\Fields;

use Code16\Sharp\Data\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

/**
 * Only for typing.
 */
#[LiteralTypeScriptType('{
    [key:string]: FormAutocompleteDynamicLocalValuesData | Array<FormAutocompleteItemData>
}')]
/**
 * @internal
 */
final class FormAutocompleteDynamicLocalValuesData extends Data {}
