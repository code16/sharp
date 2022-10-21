<?php

namespace Code16\Sharp\Utils\Entities;

interface SharpEntityResolver
{
    public function entityClassName(string $entityKey): ?string;
}
