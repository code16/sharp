<?php

namespace App\Sharp\TestForm;

use Code16\Sharp\Show\Layout\ShowLayout;
use Code16\Sharp\Show\SharpSingleShow;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class TestShow extends SharpSingleShow
{
    public function buildShowFields(FieldsContainer $showFields): void
    {
    }

    public function buildShowLayout(ShowLayout $showLayout): void
    {
    }

    public function findSingle(): array
    {
        return [];
    }
}
