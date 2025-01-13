<?php

namespace Code16\Sharp\Utils\Entities\ValueObjects;

use Illuminate\Contracts\Routing\UrlRoutable;
use Stringable;

/**
 * @internal
 */
class EntityKey implements UrlRoutable, Stringable
{
    public function __construct(
        protected ?string $key = null
    ) {
    }
    
    public function subEntity(): ?string
    {
        return str_contains($this->key, ':')
            ? str_split($this->key, ':')[1]
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
