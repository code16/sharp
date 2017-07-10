<?php

namespace Code16\Sharp\Tests\Unit\EntityList;

use Code16\Sharp\EntityList\EntityListFilter;
use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Tests\Unit\EntityList\Utils\SharpEntityDefaultTestList;

class SharpEntityListFilterTest extends SharpTestCase
{

    /** @test */
    function we_can_get_list_filters_config_with_an_instance()
    {
        $list = new class extends SharpEntityDefaultTestList {
            function buildListConfig()
            {
                $this->addFilter("test", new class implements EntityListFilter {
                    public function values() { return [1 => "A", 2 => "B"]; }
                    public function multiple() { return false; }
                });
            }
        };

        $this->assertArraySubset([
            "filters" => [
                [
                    "key" => "test",
                    "multiple" => false,
                    "values" => [1 => "A", 2 => "B"]
                ]
            ]
        ], $list->listConfig());
    }

    /** @test */
    function we_can_get_list_filters_config_with_a_class_name()
    {
        $list = new class extends SharpEntityDefaultTestList {
            function buildListConfig()
            {
                $this->addFilter("test", SharpEntityListTestFilter::class);
            }
        };

        $this->assertArraySubset([
            "filters" => [
                [
                    "key" => "test",
                    "multiple" => false,
                    "values" => [1 => "A", 2 => "B"]
                ]
            ]
        ], $list->listConfig());
    }
}

class SharpEntityListTestFilter implements EntityListFilter
{
    public function values()
    {
        return [1 => "A", 2 => "B"];
    }
    public function multiple()
    {
        return false;
    }
}