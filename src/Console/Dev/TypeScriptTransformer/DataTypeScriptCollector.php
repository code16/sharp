<?php

namespace Code16\Sharp\Console\Dev\TypeScriptTransformer;

use Code16\Sharp\Data\Data;
use ReflectionClass;
use Spatie\TypeScriptTransformer\Collectors\Collector;
use Spatie\TypeScriptTransformer\Structures\TransformedType;

/**
 * Based on https://github.com/spatie/laravel-data/blob/main/src/Support/TypeScriptTransformer/DataTypeScriptCollector.php
 */
class DataTypeScriptCollector extends Collector
{
    public function getTransformedType(ReflectionClass $class): ?TransformedType
    {
        if (! $class->isSubclassOf(Data::class)) {
            return null;
        }

        $transformer = new DataTypeScriptTransformer($this->config);

        return $transformer->transform($class, $class->getShortName());
    }
}
