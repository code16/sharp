<?php

namespace Code16\Sharp\Tests\Unit\EntityList;

use Carbon\Carbon;
use Code16\Sharp\EntityList\EntityListDateRangeFilter;
use Code16\Sharp\EntityList\EntityListDateRangeRequiredFilter;
use Code16\Sharp\EntityList\EntityListSelectFilter;
use Code16\Sharp\EntityList\EntityListSelectMultipleFilter;
use Code16\Sharp\EntityList\EntityListSelectRequiredFilter;
use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Tests\Unit\EntityList\Utils\SharpEntityDefaultTestList;

class EntityListFilterTest extends SharpTestCase
{
    /** @test */
    public function we_can_get_list_filters_config_with_an_instance()
    {
        $list = new class() extends SharpEntityDefaultTestList
        {
            public function buildListConfig(): void
            {
                $this->addFilter('test', new class() implements EntityListSelectFilter
                {
                    public function values(): array
                    {
                        return [1 => 'A', 2 => 'B'];
                    }
                }, );
            }
        };

        $list->buildListConfig();

        $this->assertArraySubset([
            'filters' => [
                [
                    'key' => 'test',
                    'label' => 'test',
                    'multiple' => false,
                    'required' => false,
                    'values' => [
                        ['id' => 1, 'label' => 'A'],
                        ['id' => 2, 'label' => 'B'],
                    ],
                ],
            ],
        ], $list->listConfig());
    }

    /** @test */
    public function we_can_get_list_filters_config_with_a_class_name()
    {
        $list = new class() extends SharpEntityDefaultTestList
        {
            public function buildListConfig(): void
            {
                $this->addFilter('test', SharpEntityListTestFilter::class);
            }
        };

        $list->buildListConfig();

        $this->assertArraySubset([
            'filters' => [
                [
                    'key' => 'test',
                    'multiple' => false,
                    'required' => false,
                    'values' => [
                        ['id' => 1, 'label' => 'A'],
                        ['id' => 2, 'label' => 'B'],
                    ],
                ],
            ],
        ], $list->listConfig());
    }

    /** @test */
    public function a_list_filters_can_be_multiple()
    {
        $list = new class() extends SharpEntityDefaultTestList
        {
            public function buildListConfig(): void
            {
                $this->addFilter('test', SharpEntityListTestMultipleFilter::class);
            }
        };

        $list->buildListConfig();

        $this->assertArraySubset([
            'filters' => [
                [
                    'key' => 'test',
                    'multiple' => true,
                    'required' => false,
                ],
            ],
        ], $list->listConfig());
    }

    /** @test */
    public function a_list_filter_can_be_required()
    {
        $list = new class() extends SharpEntityDefaultTestList
        {
            public function buildListConfig(): void
            {
                $this->addFilter('test', SharpEntityListTestRequiredFilter::class);
            }
        };

        $list->buildListConfig();

        $this->assertArraySubset([
            'filters' => [
                [
                    'key' => 'test',
                    'multiple' => false,
                    'required' => true,
                    'values' => [
                        ['id' => 1, 'label' => 'A'],
                        ['id' => 2, 'label' => 'B'],
                    ],
                    'default' => 2,
                ],
            ],
        ], $list->listConfig());
    }

    /** @test */
    public function we_can_define_a_label_for_the_filter()
    {
        $list = new class() extends SharpEntityDefaultTestList
        {
            public function buildListConfig(): void
            {
                $this->addFilter('test', new class() extends SharpEntityListTestFilter
                {
                    public function label()
                    {
                        return 'test label';
                    }
                }, );
            }
        };

        $list->buildListConfig();

        $this->assertArraySubset([
            'filters' => [
                [
                    'key' => 'test',
                    'label' => 'test label',
                ],
            ],
        ], $list->listConfig());
    }

    /** @test */
    public function we_can_define_that_a_filter_is_master()
    {
        $list = new class() extends SharpEntityDefaultTestList
        {
            public function buildListConfig(): void
            {
                $this->addFilter('test', new class() extends SharpEntityListTestFilter
                {
                    public function isMaster()
                    {
                        return true;
                    }
                }, );
            }
        };

        $list->buildListConfig();

        $this->assertArraySubset([
            'filters' => [
                [
                    'key' => 'test',
                    'master' => true,
                ],
            ],
        ], $list->listConfig());
    }

    /** @test */
    public function we_can_define_that_a_filter_is_searchable()
    {
        $list = new class() extends SharpEntityDefaultTestList
        {
            public function buildListConfig(): void
            {
                $this->addFilter('test', new class() extends SharpEntityListTestFilter
                {
                    public function isSearchable()
                    {
                        return true;
                    }
                }, );
            }
        };

        $list->buildListConfig();

        $this->assertArraySubset([
            'filters' => [
                [
                    'key' => 'test',
                    'searchable' => true,
                    'searchKeys' => ['label'],
                ],
            ],
        ], $list->listConfig());
    }

    /** @test */
    public function we_can_define_searchKeys_on_a_filter()
    {
        $list = new class() extends SharpEntityDefaultTestList
        {
            public function buildListConfig(): void
            {
                $this->addFilter('test', new class() extends SharpEntityListTestFilter
                {
                    public function isSearchable()
                    {
                        return true;
                    }

                    public function searchKeys()
                    {
                        return ['a', 'b'];
                    }
                }, );
            }
        };

        $list->buildListConfig();

        $this->assertArraySubset([
            'filters' => [
                [
                    'key' => 'test',
                    'searchable' => true,
                    'searchKeys' => ['a', 'b'],
                ],
            ],
        ], $list->listConfig());
    }

    /** @test */
    public function we_can_define_an_inline_template_for_a_filter()
    {
        $list = new class() extends SharpEntityDefaultTestList
        {
            public function buildListConfig(): void
            {
                $this->addFilter('test', new class() extends SharpEntityListTestFilter
                {
                    public function values(): array
                    {
                        return [
                            ['id' => 1, 'letter' => 'a', 'maj' => 'A'],
                            ['id' => 2, 'letter' => 'b', 'maj' => 'B'],
                        ];
                    }

                    public function template()
                    {
                        return '{{letter}} {{maj}}';
                    }
                }, );
            }
        };

        $list->buildListConfig();

        $this->assertArraySubset([
            'filters' => [
                [
                    'key' => 'test',
                    'values' => [
                        ['id' => 1, 'letter' => 'a', 'maj' => 'A'],
                        ['id' => 2, 'letter' => 'b', 'maj' => 'B'],
                    ],
                    'template' => '{{letter}} {{maj}}',
                ],
            ],
        ], $list->listConfig());
    }

    /** @test */
    public function we_can_define_that_a_filter_is_retained_and_sets_its_default_value()
    {
        $list = new class() extends SharpEntityDefaultTestList
        {
            public function buildListConfig(): void
            {
                $this->addFilter('test', new class() extends SharpEntityListTestFilter
                {
                    public function retainValueInSession()
                    {
                        return true;
                    }
                }, );
            }
        };

        // Artificially put retained value in session
        session()->put('_sharp_retained_filter_test', 2);

        $list->buildListConfig();

        $this->assertArraySubset([
            'filters' => [
                [
                    'key' => 'test',
                    'default' => 2,
                ],
            ],
        ], $list->listConfig());
    }

    /** @test */
    public function a_required_and_retained_filters_returns_retained_value_as_its_default_value()
    {
        $list = new class() extends SharpEntityDefaultTestList
        {
            public function buildListConfig(): void
            {
                $this->addFilter('test', new class() extends SharpEntityListTestRequiredFilter
                {
                    public function retainValueInSession()
                    {
                        return true;
                    }

                    public function defaultValue()
                    {
                        return 1;
                    }
                }, );
            }
        };

        // Artificially put retained value in session
        session()->put('_sharp_retained_filter_test', 2);

        $list->buildListConfig();

        $this->assertArraySubset([
            'filters' => [
                [
                    'key' => 'test',
                    'default' => 2,
                ],
            ],
        ], $list->listConfig());
    }

    /** @test */
    public function date_range_filter_retained_value_is_formatted()
    {
        $list = new class() extends SharpEntityDefaultTestList
        {
            public function buildListConfig(): void
            {
                $this->addFilter('test', new class() extends SharpEntityListDateRangeTestFilter
                {
                    public function retainValueInSession()
                    {
                        return true;
                    }
                }, );
            }
        };

        // Artificially put retained value in session
        session()->put('_sharp_retained_filter_test', '20190922..20190925');

        $list->buildListConfig();

        $this->assertArraySubset([
            'filters' => [
                [
                    'key' => 'test',
                    'default' => [
                        'start' => '2019-09-22',
                        'end' => '2019-09-25',
                    ],
                ],
            ],
        ], $list->listConfig());
    }

    /** @test */
    public function we_can_get_list_date_range_filters_config_with_a_class_name()
    {
        $list = new class() extends SharpEntityDefaultTestList
        {
            public function buildListConfig(): void
            {
                $this->addFilter('test', SharpEntityListDateRangeTestFilter::class);
            }
        };

        $list->buildListConfig();

        $this->assertArraySubset([
            'filters' => [
                [
                    'key' => 'test',
                    'type' => 'daterange',
                    'required' => false,
                ],
            ],
        ], $list->listConfig());
    }

    /** @test */
    public function a_date_range_filter_can_be_required()
    {
        $list = new class() extends SharpEntityDefaultTestList
        {
            public function buildListConfig(): void
            {
                $this->addFilter('test', SharpEntityListDateRangeRequiredTestFilter::class);
            }
        };

        $list->buildListConfig();

        $this->assertArraySubset([
            'filters' => [
                [
                    'default' => [
                        'start' => Carbon::now()->subDay()->format('Y-m-d'),
                        'end' => Carbon::now()->format('Y-m-d'),
                    ],
                ],
            ],
        ], $list->listConfig());
    }

    /** @test */
    public function we_can_define_a_date_display_format_for_a_date_range_filter()
    {
        $list = new class() extends SharpEntityDefaultTestList
        {
            public function buildListConfig(): void
            {
                $this->addFilter('test', new class() extends SharpEntityListDateRangeTestFilter
                {
                    public function dateFormat()
                    {
                        return 'YYYY-MM-DD';
                    }
                }, );
            }
        };

        $list->buildListConfig();

        $this->assertArraySubset([
            'filters' => [
                [
                    'key' => 'test',
                    'displayFormat' => 'YYYY-MM-DD',
                ],
            ],
        ], $list->listConfig());
    }

    /** @test */
    public function we_can_define_the_monday_first_attribute_for_a_date_range_filter()
    {
        $list = new class() extends SharpEntityDefaultTestList
        {
            public function buildListConfig(): void
            {
                $this->addFilter('test', new class() extends SharpEntityListDateRangeTestFilter
                {
                    public function isMondayFirst()
                    {
                        return false;
                    }
                }, );
            }
        };

        $list->buildListConfig();

        $this->assertArraySubset([
            'filters' => [
                [
                    'key' => 'test',
                    'mondayFirst' => false,
                ],
            ],
        ], $list->listConfig());
    }
}

class SharpEntityListTestFilter implements EntityListSelectFilter
{
    public function values(): array
    {
        return [1 => 'A', 2 => 'B'];
    }
}

class SharpEntityListTestMultipleFilter implements EntityListSelectMultipleFilter
{
    public function values(): array
    {
        return [1 => 'A', 2 => 'B'];
    }
}

class SharpEntityListTestRequiredFilter implements EntityListSelectRequiredFilter
{
    public function values(): array
    {
        return [1 => 'A', 2 => 'B'];
    }

    public function defaultValue()
    {
        return 2;
    }
}

class SharpEntityListDateRangeTestFilter implements EntityListDateRangeFilter
{
}

class SharpEntityListDateRangeRequiredTestFilter implements EntityListDateRangeRequiredFilter
{
    public function defaultValue(): array
    {
        return ['start' => Carbon::now()->subDay(), 'end' => Carbon::now()];
    }
}
