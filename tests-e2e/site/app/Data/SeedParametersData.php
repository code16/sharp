<?php

namespace App\Data;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class SeedParametersData
{
    public function __construct(
        public bool $tags = false,
        public bool $entityList = false,
    ) {
    }
}
