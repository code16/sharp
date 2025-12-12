<?php

namespace App\Sharp;

use Code16\Sharp\Filters\GlobalRequiredFilter;

class DummyGlobalFilter2 extends GlobalRequiredFilter
{
    public function values(): array
    {
        return [
            'fr' => 'Français',
            'de' => 'Allemand',
        ];
    }

    public function defaultValue(): mixed
    {
        return 'de';
    }

    public function authorize(): bool
    {
        return auth()->id() === 1;
    }
}
