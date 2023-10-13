<?php

namespace Code16\Sharp\Tests\Unit\Show\Fakes;

use Code16\Sharp\Show\Layout\ShowLayout;
use Code16\Sharp\Show\SharpShow;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class FakeSharpShow extends SharpShow
{
    public function find($id): array
    {
    }

    public function buildShowFields(FieldsContainer $showFields): void
    {
    }

    public function buildShowLayout(ShowLayout $showLayout): void
    {
    }

    public function delete(mixed $id): void
    {
    }
}
