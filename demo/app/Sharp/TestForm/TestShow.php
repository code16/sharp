<?php

namespace App\Sharp\TestForm;

use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayout;
use Code16\Sharp\Show\Layout\ShowLayoutColumn;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Show\SharpSingleShow;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class TestShow extends SharpSingleShow
{
    public function buildShowFields(FieldsContainer $showFields): void
    {
        $showFields->addField(SharpShowTextField::make('message'));
    }

    public function buildShowLayout(ShowLayout $showLayout): void
    {
        $showLayout->addSection('', function (ShowLayoutSection $section) {
            $section->addColumn(12, function (ShowLayoutColumn $column) {
                $column->withSingleField('message');
            });
        });
    }

    public function findSingle(): array
    {
        return [
            'message' => '<h2 class="text-center my-5 py-5">Please stay calm,<br>this is a test.</h2>',
        ];
    }
}
