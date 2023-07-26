<?php

namespace Code16\Sharp\Console\Dev\TypeScriptTransformer;

use Code16\Sharp\Data\Data;
use ReflectionClass;
use Spatie\TypeScriptTransformer\Transformers\DtoTransformer;

/**
 * Here we simply default DtoTransformer because it's enough to convert properties & collection
 */
class DataTypeScriptTransformer extends DtoTransformer
{
}
