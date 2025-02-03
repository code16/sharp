<?php

namespace Code16\Sharp\Data\Form\Fields\Common;

use Code16\Sharp\Data\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

/**
 * Only for typing.
 */
#[LiteralTypeScriptType('{ 
    [key:string]: FormDynamicOptionsData | Array<{ id: string|number, label: string }> 
}')]
/**
 * @internal
 */
final class FormDynamicOptionsData extends Data {}
