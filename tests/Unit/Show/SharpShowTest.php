<?php

namespace Code16\Sharp\Tests\Unit\Show;

use Code16\Sharp\Show\Fields\SharpShowEntityListField;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayoutColumn;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Show\SharpShow;
use Code16\Sharp\Show\SharpSingleShow;
use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class SharpShowTest extends SharpTestCase
{

    /** @test */
    function we_can_add_and_entity_list_section_to_the_layout()
    {
        $sharpShow = new class extends \Code16\Sharp\Tests\Unit\Show\BaseSharpShow
        {
            function buildShowFields(FieldsContainer $showFields): void
            {
                $showFields->addField(
                    SharpShowEntityListField::make("entityList", "entityKey")
                        ->setLabel("Test")
                );
            }
            function buildShowLayout(): void
            {
                $this->addEntityListSection("entityList");
            }
        };

        $this->assertEquals([
            "sections" => [
                [
                    "collapsable" => false,
                    "title" => "",
                    "columns" => [
                        [
                            "size" => 12,
                            "fields" => [
                                [
                                    [
                                        "key" => "entityList",
                                        "size" => 12,
                                        "sizeXS" => 12,
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ], $sharpShow->showLayout());
    }

    /** @test */
    function we_can_declare_a_collapsable_section()
    {
        $sharpShow = new class extends \Code16\Sharp\Tests\Unit\Show\BaseSharpShow
        {
            function buildShowFields(FieldsContainer $showFields): void
            {
                $showFields->addField(
                    SharpShowTextField::make("test")
                        ->setLabel("Test")
                );
            }
            function buildShowLayout(): void
            {
                $this->addSection("test", function(ShowLayoutSection $section) {
                    $section->setCollapsable()
                        ->addColumn(12, function(ShowLayoutColumn $column) {
                            $column->withSingleField("test");
                        });
                });
            }
        };

        $this->assertEquals([
            "sections" => [
                [
                    "collapsable" => true,
                    "title" => "test",
                    "columns" => [
                        [
                            "size" => 12,
                            "fields" => [
                                [
                                    [
                                        "key" => "test",
                                        "size" => 12,
                                        "sizeXS" => 12,
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ], $sharpShow->showLayout());
    }

    /** @test */
    function we_can_declare_a_multiformAttribute()
    {
        $sharpShow = new class extends \Code16\Sharp\Tests\Unit\Show\BaseSharpShow
        {
            public function buildShowConfig(): void
            {
                $this->configureMultiformAttribute("role");
            }
        };

        $sharpShow->buildShowConfig();

        $this->assertEquals([
            "multiformAttribute" => "role",
        ], $sharpShow->showConfig(1));
    }

    /** @test */
    function single_shows_have_are_declared_in_config()
    {
        $sharpShow = new class extends \Code16\Sharp\Tests\Unit\Show\BaseSharpSingleShow
        {
        };

        $this->assertEquals(
            [
                "multiformAttribute" => null,
                "isSingle" => true
            ], 
            $sharpShow->showConfig(null)
        );
    }
}

class BaseSharpShow extends SharpShow
{
    function find($id): array
    {
    }
    function buildShowFields(FieldsContainer $showFields): void
    {
    }
    function buildShowLayout(): void
    {
    }
}

class BaseSharpSingleShow extends SharpSingleShow
{
    function buildShowFields(FieldsContainer $showFields): void
    {
    }
    function buildShowLayout(): void
    {
    }
    function findSingle(): array
    {
    }
}