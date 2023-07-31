<?php

namespace Code16\Sharp\Data;

use Illuminate\Contracts\Support\Arrayable;
use ReflectionClass;
use ReflectionProperty;

abstract class Data implements Arrayable
{
    protected static array $propertyCache = [];

    public static function collection($payload): DataCollection
    {
        return DataCollection::make($payload)
            ->map(function ($item) {
                if(is_array($item)) {
                    if(method_exists(static::class, 'from')) {
                        return static::from($item);
                    }
                    return new static(...$item);
                }
                return $item;
            });
    }

    public function toArray(): array
    {
        return $this->extractPublicProperties();
    }

    protected function extractPublicProperties(): array
    {
        $class = get_class($this);

        if (! isset(static::$propertyCache[$class])) {
            $reflection = new ReflectionClass($this);

            static::$propertyCache[$class] = collect($reflection->getProperties(ReflectionProperty::IS_PUBLIC))
                ->reject(function (ReflectionProperty $property) {
                    return $property->isStatic();
                })
                ->map(function (ReflectionProperty $property) {
                    return $property->getName();
                })->all();
        }

        $values = [];

        foreach (static::$propertyCache[$class] as $property) {
            $values[$property] = $this->{$property};
        }

        return $values;
    }
}
