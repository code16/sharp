<?php

namespace Code16\Sharp\Tests\Unit\Show\Layout;

use Code16\Sharp\Show\Layout\ShowLayoutColumn;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Show\SharpShow;
use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class ShowLayoutTest extends SharpTestCase
{

    /** @test */
    function we_can_add_a_section()
    {
        $show = new class extends ShowLayoutTestShow {
            function buildShowLayout(): void
            {
                $this->addSection("label");
            }
        };

        $this->assertCount(1, $show->showLayout()["sections"]);
    }

    /** @test */
    function we_can_add_a_column_to_a_section()
    {
        $show = new class extends ShowLayoutTestShow {
            function buildShowLayout(): void
            {
                $this->addSection("label", function (ShowLayoutSection $section) {
                    $section->addColumn(7);
                });
            }
        };
        
        $this->assertCount(1, $show->showLayout()["sections"][0]["columns"]);
        $this->assertEquals(7, $show->showLayout()["sections"][0]["columns"][0]["size"]);
    }

    /** @test */
    function we_can_add_a_field_to_a_column()
    {
        $show = new class extends ShowLayoutTestShow {
            function buildShowLayout(): void
            {
                $this->addSection("label", function (ShowLayoutSection $section) {
                    $section->addColumn(7, function(ShowLayoutColumn $column) {
                        $column->withSingleField("name");
                    });
                });
            }
        };
        
        $this->assertCount(1, $show->showLayout()["sections"][0]["columns"][0]["fields"]);
        $this->assertEqualsCanonicalizing(
            [
                "key" => "name",
                "size" => 12,
                "sizeXS" => 12,
            ], 
            $show->showLayout()["sections"][0]["columns"][0]["fields"][0][0]
        );
    }

    /** @test */
    function we_can_add_a_field_with_layout_to_a_column()
    {
        $show = new class extends ShowLayoutTestShow {
            function buildShowLayout(): void
            {
                $this->addSection("label", function (ShowLayoutSection $section) {
                    $section->addColumn(7, function(ShowLayoutColumn $column) {
                        $column->withSingleField("list", function(ShowLayoutColumn $listItem) {
                            $listItem->withSingleField("item");
                        });
                    });
                });
            }
        };
        
        $this->assertCount(1, $show->showLayout()["sections"][0]["columns"][0]["fields"]);
        $this->assertEqualsCanonicalizing(
            [
                "key" => "list",
                "size" => 12,
                "sizeXS" => 12,
                "item" => [
                    [
                        [
                            "key" => "item",
                            "size" => 12,
                            "sizeXS" => 12
                        ]
                    ]
                ]
            ],
            $show->showLayout()["sections"][0]["columns"][0]["fields"][0][0]
        );
    }
}

abstract class ShowLayoutTestShow extends SharpShow
{
    function find($id): array { return []; }
    function buildShowFields(FieldsContainer $showFields): void {}
    function buildShowLayout(): void {}
}