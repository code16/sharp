<?php

namespace App\Sharp\TestModels\Single;

use App\Models\TestModel;
use Code16\Sharp\Show\Layout\ShowLayout;
use Code16\Sharp\Show\SharpSingleShow;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class TestModelSingleShow extends SharpSingleShow
{
    protected function buildShowFields(FieldsContainer $showFields): void
    {
        // TODO: Implement buildShowFields() method.
    }

    protected function buildShowLayout(ShowLayout $showLayout): void
    {
        // TODO: Implement buildShowLayout() method.
    }

    public function buildShowConfig(): void
    {
        $this->configurePageTitleAttribute('text');
    }

    public function findSingle(): array
    {
        return $this->transform(TestModel::first());
    }
}
