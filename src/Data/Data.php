<?php

namespace Code16\Sharp\Data;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use ReflectionClass;
use ReflectionParameter;
use ReflectionProperty;
use Spatie\TypeScriptTransformer\Attributes\Optional;
use function Pest\Laravel\json;

abstract class Data implements Arrayable
{
    /** @var ReflectionParameter[][] */
    protected static array $constructorParameterCache = [];

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

    public static function optional(mixed $payload): ?static
    {
        if(is_null($payload)) {
            return null;
        }

        return static::from($payload);
    }

    public function toArray(): array
    {
        return $this->transformValues($this->extractValuesFromConstructor());
    }

    protected function extractValuesFromConstructor(): array
    {
        $class = get_class($this);

        if (! isset(static::$constructorParameterCache[$class])) {
            $reflection = new ReflectionClass($this);

            static::$constructorParameterCache[$class] = $reflection->getConstructor()->getParameters();
        }

        $values = [];

        foreach (static::$constructorParameterCache[$class] as $parameter) {
            $name = $parameter->getName();
            if ($parameter->isOptional() && $parameter->getDefaultValue() === null && $this->{$parameter->name} === null) {
                continue;
            }
            $values[$parameter->getName()] = $this->{$parameter->getName()};
        }

        return $values;
    }

    protected function transformValues(array $values): array
    {
        return collect($values)
            ->map(function ($value) {
                if($value instanceof \BackedEnum) {
                    return $value->value;
                }
                return $value;
            })
            ->toArray();
    }
}
