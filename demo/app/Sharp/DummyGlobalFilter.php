<?php

namespace App\Sharp;

use Code16\Sharp\Utils\Filters\GlobalRequiredFilter;

class DummyGlobalFilter extends GlobalRequiredFilter
{
    public function values(): array
    {
        return [
            '1' => 'Tenant 1',
            '2' => 'Tenant 2',
        ];
    }

    public function defaultValue(): mixed
    {
        return '1';
    }
}
