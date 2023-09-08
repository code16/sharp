<?php

namespace Code16\Sharp\Tests\Unit\Show\Fakes;

use Code16\Sharp\Show\Layout\ShowLayout;
use Code16\Sharp\Show\SharpShow;
use Code16\Sharp\Show\SharpSingleShow;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class FakeSharpSingleShow extends SharpSingleShow
{
    protected function buildShowFields(FieldsContainer $showFields): void
    {
    }

    protected function buildShowLayout(ShowLayout $showLayout): void
    {
    }

    public function findSingle(): array
    {
    }
}