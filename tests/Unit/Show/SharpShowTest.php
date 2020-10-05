<?php

namespace Code16\Sharp\Tests\Unit\Show;

use Code16\Sharp\Show\Fields\SharpShowEntityListField;
use Code16\Sharp\Show\SharpShow;
use Code16\Sharp\Show\SharpSingleShow;
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
                        ->setLabel("Test")
                    
                );
            }
            function buildShowLayout()
            {
                $this->addEntityListSection("entityList");
            }
        };

        $this->assertEquals([
            "sections" => [
                [
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
    function single_shows_have_are_declared_in_config()
    {
        $sharpShow = new class extends \Code16\Sharp\Tests\Unit\Show\BaseSharpSingleShow
        {
        };

        $this->assertEquals([
            "isSingle" => true
        ], $sharpShow->showConfig(null));
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

class BaseSharpSingleShow extends SharpSingleShow
{
    function buildShowFields()
    {
    }
    function buildShowLayout()
    {
    }
    function findSingle(): array
    {
    }
}