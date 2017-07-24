<?php

namespace Code16\Sharp\Tests\Unit\EntityList;

use Code16\Sharp\EntityList\EntityListFilter;
use Code16\Sharp\EntityList\EntityListMultipleFilter;
use Code16\Sharp\EntityList\EntityListRequiredFilter;
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
                });
            }
        };

        $list->buildListConfig();

        $this->assertArraySubset([
            "filters" => [
                [
                    "key" => "test",
                    "multiple" => false,
                    "required" => false,
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

        $list->buildListConfig();

        $this->assertArraySubset([
            "filters" => [
                [
                    "key" => "test",
                    "multiple" => false,
                    "required" => false,
                    "values" => [1 => "A", 2 => "B"]
                ]
            ]
        ], $list->listConfig());
    }

    /** @test */
    function a_list_filters_can_be_multiple()
    {
        $list = new class extends SharpEntityDefaultTestList {
            function buildListConfig()
            {
                $this->addFilter("test", SharpEntityListTestMultipleFilter::class);
            }
        };

        $list->buildListConfig();

        $this->assertArraySubset([
            "filters" => [
                [
                    "key" => "test",
                    "multiple" => true,
                    "required" => false,
                    "values" => [1 => "A", 2 => "B"]
                ]
            ]
        ], $list->listConfig());
    }

    /** @test */
    function a_list_filter_can_be_required()
    {
        $list = new class extends SharpEntityDefaultTestList {
            function buildListConfig()
            {
                $this->addFilter("test", SharpEntityListTestRequiredFilter::class);
            }
        };

        $list->buildListConfig();

        $this->assertArraySubset([
            "filters" => [
                [
                    "key" => "test",
                    "multiple" => false,
                    "required" => true,
                    "values" => [1 => "A", 2 => "B"],
                    "default" => 2
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
}

class SharpEntityListTestMultipleFilter implements EntityListMultipleFilter
{
    public function values()
    {
        return [1 => "A", 2 => "B"];
    }
}

class SharpEntityListTestRequiredFilter implements EntityListRequiredFilter
{
    public function values()
    {
        return [1 => "A", 2 => "B"];
    }
    public function defaultValue()
    {
        return 2;
    }
}