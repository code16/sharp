<?php

use Code16\Sharp\Show\Layout\ShowLayout;
use Code16\Sharp\Show\Layout\ShowLayoutColumn;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Tests\Unit\Show\Fakes\FakeSharpShow;

it('handles_sections', function () {
    $show = new class extends FakeSharpShow
    {
        public function buildShowLayout(ShowLayout $showLayout): void
        {
            $showLayout->addSection('label');
        }
    };

    expect($show->showLayout()['sections'][0]['title'])->toEqual('label');
});

it('handles columns in sections', function () {
    $show = new class extends FakeSharpShow
    {
        public function buildShowLayout(ShowLayout $showLayout): void
        {
            $showLayout->addSection('label', function (ShowLayoutSection $section) {
                $section->addColumn(7);
            });
        }
    };

    expect($show->showLayout()['sections'][0]['columns'])->toHaveCount(1);
    expect($show->showLayout()['sections'][0]['columns'][0]['size'])->toEqual(7);
});

it('handles fields in columns', function () {
    $show = new class extends FakeSharpShow
    {
        public function buildShowLayout(ShowLayout $showLayout): void
        {
            $showLayout->addSection('label', function (ShowLayoutSection $section) {
                $section->addColumn(7, function (ShowLayoutColumn $column) {
                    $column->withSingleField('name');
                });
            });
        }
    };

    expect($show->showLayout()['sections'][0]['columns'][0]['fields'])->toHaveCount(1);
    expect($show->showLayout()['sections'][0]['columns'][0]['fields'][0][0])->toEqual([
        'key' => 'name',
        'size' => 12,
        'sizeXS' => 12,
    ]);
});

it('handles fields with layout', function () {
    $show = new class extends FakeSharpShow
    {
        public function buildShowLayout(ShowLayout $showLayout): void
        {
            $showLayout->addSection('label', function (ShowLayoutSection $section) {
                $section->addColumn(7, function (ShowLayoutColumn $column) {
                    $column->withSingleField('list', function (ShowLayoutColumn $listItem) {
                        $listItem->withSingleField('item');
                    });
                });
            });
        }
    };

    expect($show->showLayout()['sections'][0]['columns'][0]['fields'])->toHaveCount(1);
    expect($show->showLayout()['sections'][0]['columns'][0]['fields'][0][0])->toEqual([
        'key' => 'list',
        'size' => 12,
        'sizeXS' => 12,
        'item' => [
            [
                [
                    'key' => 'item',
                    'size' => 12,
                    'sizeXS' => 12,
                ],
            ],
        ],
    ]);
});
