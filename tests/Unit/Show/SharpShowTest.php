<?php

namespace Code16\Sharp\Tests\Unit\Show;

use Code16\Sharp\Show\Fields\SharpShowEntityListField;
use Code16\Sharp\Show\SharpShow;
use Code16\Sharp\Tests\SharpTestCase;

class SharpShowTest extends SharpTestCase
{

    /** @test */
    function we_can_add_and_entity_list_section_to_the_layout()
    {
        $sharpShow = new class extends \Code16\Sharp\Tests\Unit\Show\BaseSharpShow
        {
            function buildShowFields()
            {
                $this->addField(
                    SharpShowEntityListField::make("entityList", "entityKey")
                );
            }
            function buildShowLayout()
            {
                $this->addEntityListSection('Test', "entityList");
            }
        };

        $this->assertEquals([
            "sections" => [
                [
                    "title" => "Test",
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
}

class BaseSharpShow extends SharpShow
{
    function find($id): array
    {
    }
    function buildShowFields()
    {
    }
    function buildShowLayout()
    {
    }
}