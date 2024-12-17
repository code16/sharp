<?php

namespace Code16\Sharp\Tests\Fixtures\Sharp;

use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayout;
use Code16\Sharp\Show\Layout\ShowLayoutColumn;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Show\SharpShow;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class PersonShow extends SharpShow
{
    public function buildShowFields(FieldsContainer $showFields): void
    {
        $showFields->addField(SharpShowTextField::make('name'));
    }

    public function buildShowConfig(): void {}

    public function buildShowLayout(ShowLayout $showLayout): void
    {
        $showLayout
            ->addSection(function (ShowLayoutSection $section) {
                $section
                    ->addColumn(6, function (ShowLayoutColumn $column) {
                        $column->withField('name');
                    });
            });
    }

    public function find($id): array
    {
        return ['name' => 'John Wayne', 'job' => 'actor', 'state' => 'active'];
    }

    public function delete($id): void {}
}
