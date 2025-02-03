<?php

namespace Code16\Sharp\Utils\Entities\ValueObjects;

use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Support\Str;
use Stringable;

/**
 * @internal
 */
class EntityKey implements Stringable, UrlRoutable
{
    public function __construct(
        protected ?string $key = null
    ) {}

    public function baseKey(): string
    {
        return str_contains($this->key, ':')
            ? Str::before($this->key, ':')
            : $this->key;
    }

    public function subEntity(): ?string
    {
        return str_contains($this->key, ':')
            ? Str::after($this->key, ':')
            : null;
    }

    public function getRouteKey()
    {
        return $this->key;
    }

    public function getRouteKeyName()
    {
        return 'key';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return new static($value);
    }

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        return null;
    }

    public function toString(): string
    {
        return $this->key;
    }

    public function __toString()
    {
        return $this->key;
    }
}
