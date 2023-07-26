<?php

namespace Code16\Sharp\Data;

use Illuminate\Contracts\Support\Arrayable;
use ReflectionClass;
use ReflectionProperty;

abstract class Data implements Arrayable
{
    public static function collection($payload): DataCollection
    {
        return new DataCollection($payload);
    }

    public function toArray()
    {
        $reflection = new ReflectionClass($this);

        return collect($reflection->getProperties(ReflectionProperty::IS_PUBLIC))
            ->reject(function (ReflectionProperty $property) {
                return $property->isStatic();
            })
            ->mapWithKeys(function (ReflectionProperty $property) {
                return [$property->getName() => $property->getValue($this)];
            })
            ->toArray();
    }
}
