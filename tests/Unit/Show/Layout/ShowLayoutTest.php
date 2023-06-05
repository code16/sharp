<?php

namespace Code16\Sharp\Tests\Unit\Show\Layout;

use Code16\Sharp\Show\Layout\ShowLayout;
use Code16\Sharp\Show\Layout\ShowLayoutColumn;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Show\SharpShow;
use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class ShowLayoutTest extends SharpTestCase
{
    /** @test */
    public function we_can_add_a_section()
    {
        $show = new class extends ShowLayoutTestShow
        {
            public function buildShowLayout(ShowLayout $showLayout): void
            {
                $showLayout->addSection('label');
            }
        };

        $this->assertCount(1, $show->showLayout()['sections']);
    }

    /** @test */
    public function we_can_add_a_column_to_a_section()
    {
        $show = new class extends ShowLayoutTestShow
        {
            public function buildShowLayout(ShowLayout $showLayout): void
            {
                $showLayout->addSection('label', function (ShowLayoutSection $section) {
                    $section->addColumn(7);
                });
            }
        };

        $this->assertCount(1, $show->showLayout()['sections'][0]['columns']);
        $this->assertEquals(7, $show->showLayout()['sections'][0]['columns'][0]['size']);
    }

    /** @test */
    public function we_can_add_a_field_to_a_column()
    {
        $show = new class extends ShowLayoutTestShow
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

        $this->assertCount(1, $show->showLayout()['sections'][0]['columns'][0]['fields']);
        $this->assertEqualsCanonicalizing(
            [
                'key' => 'name',
                'size' => 12,
                'sizeXS' => 12,
            ],
            $show->showLayout()['sections'][0]['columns'][0]['fields'][0][0],
        );
    }

    /** @test */
    public function we_can_add_a_field_with_layout_to_a_column()
    {
        $show = new class extends ShowLayoutTestShow
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

        $this->assertCount(1, $show->showLayout()['sections'][0]['columns'][0]['fields']);
        $this->assertEqualsCanonicalizing(
            [
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
            ],
            $show->showLayout()['sections'][0]['columns'][0]['fields'][0][0],
        );
    }
}

abstract class ShowLayoutTestShow extends SharpShow
{
    public function find($id): array
    {
        return [];
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
