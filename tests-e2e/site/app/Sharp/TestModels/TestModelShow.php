<?php

namespace App\Sharp\TestModels;

use App\Models\TestModel;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayout;
use Code16\Sharp\Show\Layout\ShowLayoutColumn;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Show\SharpShow;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class TestModelShow extends SharpShow
{
    protected function buildShowFields(FieldsContainer $showFields): void
    {
        $showFields
            ->addField(
                SharpShowTextField::make('text')
                    ->setLabel('Text')
            );
    }

    protected function buildShowLayout(ShowLayout $showLayout): void
    {
        $showLayout
            ->addSection('Section 1', function (ShowLayoutSection $section) {
                $section
                    ->addColumn(12, function (ShowLayoutColumn $column) {
                        $column->withField('text');
                    });
            });
    }

    public function buildShowConfig(): void
    {
    }

    public function find($id): array
    {
        return $this->transform(TestModel::findOrFail($id));
    }

    public function delete($id): void
    {
        TestModel::findOrFail($id)->delete();
    }
}