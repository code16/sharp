<?php

namespace App\Sharp;

use Code16\Sharp\Utils\Filters\GlobalRequiredFilter;

class MyGlobalFilter extends GlobalRequiredFilter
{
    public function values(): array
    {
        return [
            "v1" => "Label 1",
            "v2" => "Label 2"
        ];
    }

    public function defaultValue(): mixed
    {
        return "v1";
    }
}