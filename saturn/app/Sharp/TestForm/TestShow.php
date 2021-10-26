<?php

namespace App\Sharp\TestForm;

use Code16\Sharp\Show\Layout\ShowLayout;
use Code16\Sharp\Show\SharpSingleShow;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class TestShow extends SharpSingleShow
{
    function buildShowFields(FieldsContainer $showFields): void
    {
    }

    function buildShowLayout(ShowLayout $showLayout): void
    {
    }

    function findSingle(): array
    {
        return [];
    }
}
