<?php

namespace Code16\Sharp\Tests\Fixtures\Sharp;

use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayout;
use Code16\Sharp\Show\Layout\ShowLayoutColumn;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Show\SharpSingleShow;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class SinglePersonShow extends SharpSingleShow
{
    public function buildShowFields(FieldsContainer $showFields): void
    {
        $showFields->addField(SharpShowTextField::make('name'));
    }

    public function buildShowConfig(): void {}

    public function buildShowLayout(ShowLayout $showLayout): void
    {
        $showLayout
            ->addSection('', function (ShowLayoutSection $section) {
                $section
                    ->addColumn(6, function (ShowLayoutColumn $column) {
                        $column->withField('name');
                    });
            });
    }

    public function findSingle(): array
    {
        return ['name' => 'Marie Curie', 'job' => 'physicist'];
    }
}
