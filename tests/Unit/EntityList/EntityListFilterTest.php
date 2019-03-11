<?php

namespace Code16\Sharp\Tests\Unit\EntityList;

use Code16\Sharp\EntityList\EntityListFilter;
use Code16\Sharp\EntityList\EntityListMultipleFilter;
use Code16\Sharp\EntityList\EntityListRequiredFilter;
use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Tests\Unit\EntityList\Utils\SharpEntityDefaultTestList;

class EntityListFilterTest extends SharpTestCase
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

        $this->assertArrayContainsSubset([
            "filters" => [
                [
                    "key" => "test",
                    "label" => "test",
                    "multiple" => false,
                    "required" => false,
                    "values" => [
                        ["id" => 1, "label" => "A"],
                        ["id" => 2, "label" => "B"]
                    ]
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

        $this->assertArrayContainsSubset([
            "filters" => [
                [
                    "key" => "test",
                    "multiple" => false,
                    "required" => false,
                    "values" => [
                        ["id" => 1, "label" => "A"],
                        ["id" => 2, "label" => "B"]
                    ]
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

        $this->assertArrayContainsSubset([
            "filters" => [
                [
                    "key" => "test",
                    "multiple" => true,
                    "required" => false,
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

        $this->assertArrayContainsSubset([
            "filters" => [
                [
                    "key" => "test",
                    "multiple" => false,
                    "required" => true,
                    "values" => [
                        ["id" => 1, "label" => "A"],
                        ["id" => 2, "label" => "B"]
                    ],
                    "default" => 2
                ]
            ]
        ], $list->listConfig());
    }

    /** @test */
    function we_can_define_a_label_for_the_filter()
    {
        $list = new class extends SharpEntityDefaultTestList {
            function buildListConfig()
            {
                $this->addFilter("test", new class extends SharpEntityListTestFilter {
                    function label()
                    {
                        return "test label";
                    }
                });
            }
        };

        $list->buildListConfig();

        $this->assertArrayContainsSubset([
            "filters" => [
                [
                    "key" => "test",
                    "label" => "test label",
                ]
            ]
        ], $list->listConfig());
    }

    /** @test */
    function we_can_define_that_a_filter_is_master()
    {
        $list = new class extends SharpEntityDefaultTestList {
            function buildListConfig()
            {
                $this->addFilter("test", new class extends SharpEntityListTestFilter {
                    function isMaster() {
                        return true;
                    }
                });
            }
        };

        $list->buildListConfig();

        $this->assertArrayContainsSubset([
            "filters" => [
                [
                    "key" => "test",
                    "master" => true,
                ]
            ]
        ], $list->listConfig());
    }

    /** @test */
    function we_can_define_that_a_filter_is_searchable()
    {
        $list = new class extends SharpEntityDefaultTestList {
            function buildListConfig()
            {
                $this->addFilter("test", new class extends SharpEntityListTestFilter {
                    function isSearchable() {
                        return true;
                    }
                });
            }
        };

        $list->buildListConfig();

        $this->assertArrayContainsSubset([
            "filters" => [
                [
                    "key" => "test",
                    "searchable" => true,
                    "searchKeys" => ["label"]
                ]
            ]
        ], $list->listConfig());
    }

    /** @test */
    function we_can_define_searchKeys_on_a_filter()
    {
        $list = new class extends SharpEntityDefaultTestList {
            function buildListConfig()
            {
                $this->addFilter("test", new class extends SharpEntityListTestFilter {
                    function isSearchable() {
                        return true;
                    }
                    function searchKeys() {
                        return ["a", "b"];
                    }
                });
            }
        };

        $list->buildListConfig();

        $this->assertArrayContainsSubset([
            "filters" => [
                [
                    "key" => "test",
                    "searchable" => true,
                    "searchKeys" => ["a", "b"]
                ]
            ]
        ], $list->listConfig());
    }

    /** @test */
    function we_can_define_an_inline_template_for_a_filter()
    {
        $list = new class extends SharpEntityDefaultTestList {
            function buildListConfig()
            {
                $this->addFilter("test", new class extends SharpEntityListTestFilter {
                    public function values()
                    {
                        return [
                            ["id"=>1, "letter"=>"a", "maj"=>"A"],
                            ["id"=>2, "letter"=>"b", "maj"=>"B"]
                        ];
                    }
                    function template() {
                        return "{{letter}} {{maj}}";
                    }
                });
            }
        };

        $list->buildListConfig();

        $this->assertArrayContainsSubset([
            "filters" => [
                [
                    "key" => "test",
                    "values" => [
                        ["id"=>1, "letter"=>"a", "maj"=>"A"],
                        ["id"=>2, "letter"=>"b", "maj"=>"B"]
                    ],
                    "template" => "{{letter}} {{maj}}"
                ]
            ]
        ], $list->listConfig());
    }

    /** @test */
    function we_can_define_that_a_filter_is_retained_and_sets_its_default_value()
    {
        $list = new class extends SharpEntityDefaultTestList {
            function buildListConfig()
            {
                $this->addFilter("test", new class extends SharpEntityListTestFilter {
                    function retainValueInSession() {
                        return true;
                    }
                });
            }
        };

        // Artificially put retained value in session
        session()->put("_sharp_retained_filter_test", 2);

        $list->buildListConfig();

        $this->assertArrayContainsSubset([
            "filters" => [
                [
                    "key" => "test",
                    "default" => 2,
                ]
            ]
        ], $list->listConfig());
    }

    /** @test */
    function a_required_and_retained_filters_returns_retained_value_as_its_default_value()
    {
        $list = new class extends SharpEntityDefaultTestList {
            function buildListConfig()
            {
                $this->addFilter("test", new class extends SharpEntityListTestRequiredFilter {
                    function retainValueInSession() {
                        return true;
                    }
                    public function defaultValue() {
                        return 1;
                    }
                });
            }
        };

        // Artificially put retained value in session
        session()->put("_sharp_retained_filter_test", 2);

        $list->buildListConfig();

        $this->assertArrayContainsSubset([
            "filters" => [
                [
                    "key" => "test",
                    "default" => 2,
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