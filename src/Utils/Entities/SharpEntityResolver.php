<?php

namespace Code16\Sharp\Utils\Entities;

/** @deprecated use the new SharpConfigBuilder workflow */
interface SharpEntityResolver
{
    public function entityClassName(string $entityKey): ?string;
}
