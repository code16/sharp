<?php

namespace Code16\Sharp\Tests\Unit\EntityList;

use Carbon\Carbon;
use Code16\Sharp\EntityList\Filters\EntityListDateRangeFilter;
use Code16\Sharp\EntityList\Filters\EntityListSelectFilter;
use Code16\Sharp\EntityList\Filters\EntityListSelectRequiredFilter;
use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Tests\Unit\EntityList\Utils\SharpEntityDefaultTestList;

class EntityListFilterTest extends SharpTestCase
{
    /** @test */
    public function we_can_get_get_filters_config_with_a_key()
    {
        $list = new class extends SharpEntityDefaultTestList
        {
            public function getFilters(): array
            {
                return [
                    new class extends EntityListSelectFilter
                    {
                        public function buildFilterConfig(): void
                        {
                            $this->configureKey('test')
                                ->configureLabel('test_label');
                        }

                        public function values(): array
                        {
                            return [1 => 'A', 2 => 'B'];
                        }
                    },
                ];
            }
        };

        $list->buildListConfig();

        $this->assertEquals(
            [
                '_page' => [
                    [
                        'key' => 'test',
                        'label' => 'test_label',
                        'multiple' => false,
                        'required' => false,
                        'values' => [
                            ['id' => 1, 'label' => 'A'],
                            ['id' => 2, 'label' => 'B'],
                        ],
                        'default' => null,
                        'type' => 'select',
                        'master' => false,
                        'searchable' => false,
                        'searchKeys' => ['label'],
                        'template' => '{{label}}',
                    ],
                ],
            ],
            $list->listConfig()['filters'],
        );
    }

    /** @test */
    public function we_can_get_get_filters_config_with_a_class_name()
    {
        $list = new class extends SharpEntityDefaultTestList
        {
            public function getFilters(): array
            {
                return [
                    SharpEntityListTestFilter::class,
                ];
            }
        };

        $list->buildListConfig();

        $this->assertEquals(
            class_basename(SharpEntityListTestFilter::class), 
            $list->listConfig()['filters']['_page'][0]['key']
        );
    }

    /** @test */
    public function we_can_get_section_based_filters_config()
    {
        $list = new class extends SharpEntityDefaultTestList
        {
            public function getFilters(): ?array
            {
                return [
                    new class extends EntityListSelectFilter
                    {
                        public function values(): array
                        {
                            return [1 => 'A', 2 => 'B'];
                        }
                    },
                    'section-1' => [
                        new class extends EntityListSelectFilter
                        {
                            public function values(): array
                            {
                                return [3 => 'C', 4 => 'D'];
                            }
                        },
                    ]
                ];
            }
        };

        $list->buildListConfig();

        $this->assertEquals(
            [
                ['id' => 1, 'label' => 'A'],
                ['id' => 2, 'label' => 'B'],
            ],
            $list->listConfig()['filters']['_page'][0]['values'],
        );

        $this->assertEquals(
            [
                ['id' => 3, 'label' => 'C'],
                ['id' => 4, 'label' => 'D'],
            ],
            $list->listConfig()['filters']['section-1'][0]['values'],
        );
    }

    /** @test */
    public function a_list_filter_can_be_multiple()
    {
        $list = new class extends SharpEntityDefaultTestList
        {
            public function getFilters(): array
            {
                return [
                    SharpEntityListTestMultipleFilter::class,
                ];
            }
        };

        $list->buildListConfig();

        $this->assertTrue($list->listConfig()['filters']['_page'][0]['multiple']);
        $this->assertFalse($list->listConfig()['filters']['_page'][0]['required']);
    }

    /** @test */
    public function a_list_filter_can_be_required()
    {
        $list = new class extends SharpEntityDefaultTestList
        {
            public function getFilters(): array
            {
                return [
                    SharpEntityListTestRequiredFilter::class,
                ];
            }
        };

        $list->buildListConfig();

        $this->assertTrue($list->listConfig()['filters']['_page'][0]['required']);
    }

    /** @test */
    public function we_can_define_a_label_for_the_filter()
    {
        $list = new class extends SharpEntityDefaultTestList
        {
            public function getFilters(): array
            {
                return [
                    new class extends SharpEntityListTestFilter
                    {
                        public function buildFilterConfig(): void
                        {
                            $this->configureLabel('test label');
                        }
                    },
                ];
            }
        };

        $list->buildListConfig();

        $this->assertEquals('test label', $list->listConfig()['filters']['_page'][0]['label']);
    }

    /** @test */
    public function we_can_define_that_a_filter_is_master()
    {
        $list = new class extends SharpEntityDefaultTestList
        {
            public function getFilters(): array
            {
                return [
                    new class extends SharpEntityListTestFilter
                    {
                        public function buildFilterConfig(): void
                        {
                            $this->configureMaster();
                        }
                    },
                ];
            }
        };

        $list->buildListConfig();

        $this->assertTrue($list->listConfig()['filters']['_page'][0]['master']);
    }

    /** @test */
    public function we_can_define_that_a_filter_is_searchable()
    {
        $list = new class extends SharpEntityDefaultTestList
        {
            public function getFilters(): array
            {
                return [
                    new class extends SharpEntityListTestFilter
                    {
                        public function buildFilterConfig(): void
                        {
                            $this->configureSearchable();
                        }
                    },
                ];
            }
        };

        $list->buildListConfig();

        $this->assertTrue($list->listConfig()['filters']['_page'][0]['searchable']);
        $this->assertEquals(['label'], $list->listConfig()['filters']['_page'][0]['searchKeys']);
    }

    /** @test */
    public function we_can_define_searchKeys_on_a_filter()
    {
        $list = new class extends SharpEntityDefaultTestList
        {
            public function getFilters(): array
            {
                return [
                    new class extends SharpEntityListTestFilter
                    {
                        public function buildFilterConfig(): void
                        {
                            $this->configureSearchable()
                                ->configureSearchKeys(['a', 'b']);
                        }
                    },
                ];
            }
        };

        $list->buildListConfig();

        $this->assertEquals(['a', 'b'], $list->listConfig()['filters']['_page'][0]['searchKeys']);
    }

    /** @test */
    public function we_can_define_an_inline_template_for_a_filter()
    {
        $list = new class extends SharpEntityDefaultTestList
        {
            public function getFilters(): array
            {
                return [
                    new class extends SharpEntityListTestFilter
                    {
                        public function buildFilterConfig(): void
                        {
                            $this->configureTemplate('{{letter}} {{maj}}');
                        }

                        public function values(): array
                        {
                            return [
                                ['id' => 1, 'letter' => 'a', 'maj' => 'A'],
                                ['id' => 2, 'letter' => 'b', 'maj' => 'B'],
                            ];
                        }
                    },
                ];
            }
        };

        $list->buildListConfig();

        $this->assertEquals('{{letter}} {{maj}}', $list->listConfig()['filters']['_page'][0]['template']);
        $this->assertEquals(
            [
                ['id' => 1, 'letter' => 'a', 'maj' => 'A'],
                ['id' => 2, 'letter' => 'b', 'maj' => 'B'],
            ], 
            $list->listConfig()['filters']['_page'][0]['values']
        );
    }

    /** @test */
    public function we_can_define_that_a_filter_is_retained_and_sets_its_default_value()
    {
        $list = new class extends SharpEntityDefaultTestList
        {
            public function getFilters(): array
            {
                return [
                    new class extends SharpEntityListTestFilter
                    {
                        public function buildFilterConfig(): void
                        {
                            $this->configureKey('test_20')
                                ->configureRetainInSession();
                        }
                    },
                ];
            }
        };

        // Artificially put retained value in session
        session()->put('_sharp_retained_filter_test_20', 2);
        $list->buildListConfig();
        
        $this->assertEquals(2, $list->listConfig()['filters']['_page'][0]['default']);
    }

    /** @test */
    public function a_required_and_retained_filters_returns_retained_value_as_its_default_value()
    {
        $list = new class extends SharpEntityDefaultTestList
        {
            public function getFilters(): array
            {
                return [
                    new class extends SharpEntityListTestRequiredFilter
                    {
                        public function buildFilterConfig(): void
                        {
                            $this->configureKey('test_21')
                                ->configureRetainInSession();
                        }

                        public function defaultValue(): mixed
                        {
                            return 1;
                        }
                    },
                ];
            }
        };

        // Artificially put retained value in session
        session()->put('_sharp_retained_filter_test_21', 2);
        $list->buildListConfig();
        
        $this->assertEquals(2, $list->listConfig()['filters']['_page'][0]['default']);
    }

    /** @test */
    public function date_range_filter_retained_value_is_formatted()
    {
        $list = new class extends SharpEntityDefaultTestList
        {
            public function getFilters(): array
            {
                return [
                    new class extends SharpEntityListDateRangeTestFilter
                    {
                        public function buildFilterConfig(): void
                        {
                            $this->configureKey('test_22')
                                ->configureRetainInSession();
                        }
                    },
                ];
            }
        };

        // Artificially put retained value in session
        session()->put('_sharp_retained_filter_test_22', '20190922..20190925');
        $list->buildListConfig();
        
        $this->assertEquals(
            [
                'start' => '2019-09-22',
                'end' => '2019-09-25',
            ],
            $list->listConfig()['filters']['_page'][0]['default']
        );
    }

    /** @test */
    public function we_can_get_list_date_range_filters_config_with_a_class_name()
    {
        $list = new class extends SharpEntityDefaultTestList
        {
            public function getFilters(): array
            {
                return [SharpEntityListDateRangeTestFilter::class];
            }
        };

        $list->buildListConfig();
        
        $this->assertEquals(
            class_basename(SharpEntityListDateRangeTestFilter::class),
            $list->listConfig()['filters']['_page'][0]['key']
        );
        $this->assertEquals(
            'daterange',
            $list->listConfig()['filters']['_page'][0]['type']
        );
    }

    /** @test */
    public function a_date_range_filter_can_be_required()
    {
        $list = new class extends SharpEntityDefaultTestList
        {
            public function getFilters(): array
            {
                return [SharpEntityListDateRangeRequiredTestFilter::class];
            }
        };

        $list->buildListConfig();
        
        $this->assertTrue($list->listConfig()['filters']['_page'][0]['required']);
        $this->assertEquals(
            [
                'start' => Carbon::now()->subDay()->format('Y-m-d'),
                'end' => Carbon::now()->format('Y-m-d'),
            ],
            $list->listConfig()['filters']['_page'][0]['default']
        );
    }

    /** @test */
    public function we_can_define_a_date_display_format_for_a_date_range_filter()
    {
        $list = new class extends SharpEntityDefaultTestList
        {
            public function getFilters(): array
            {
                return [
                    new class extends SharpEntityListDateRangeTestFilter
                    {
                        public function buildFilterConfig(): void
                        {
                            $this->configureDateFormat('YYYY-MM-DD');
                        }
                    },
                ];
            }
        };

        $list->buildListConfig();
        
        $this->assertEquals(
            'YYYY-MM-DD',
            $list->listConfig()['filters']['_page'][0]['displayFormat']
        );
    }

    /** @test */
    public function we_can_define_the_monday_first_attribute_for_a_date_range_filter()
    {
        $list = new class extends SharpEntityDefaultTestList
        {
            public function getFilters(): array
            {
                return [
                    new class extends SharpEntityListDateRangeTestFilter
                    {
                        public function buildFilterConfig(): void
                        {
                            $this->configureMondayFirst(false);
                        }
                    },
                ];
            }
        };

        $list->buildListConfig();
        
        $this->assertFalse($list->listConfig()['filters']['_page'][0]['mondayFirst']);
    }

    /** @test */
    public function we_can_define_a_check_filter()
    {
        $list = new class extends SharpEntityDefaultTestList
        {
            public function getFilters(): array
            {
                return [
                    SharpEntityListTestCheckFilter::class,
                ];
            }
        };

        $list->buildListConfig();

        $this->assertEquals(class_basename(SharpEntityListTestCheckFilter::class), $list->listConfig()['filters']['_page'][0]['key']);
        $this->assertEquals('check', $list->listConfig()['filters']['_page'][0]['type']);
    }
}

class SharpEntityListTestFilter extends \Code16\Sharp\EntityList\Filters\EntityListSelectFilter
{
    public function values(): array
    {
        return [1 => 'A', 2 => 'B'];
    }
}

class SharpEntityListTestMultipleFilter extends \Code16\Sharp\EntityList\Filters\EntityListSelectMultipleFilter
{
    public function values(): array
    {
        return [1 => 'A', 2 => 'B'];
    }
}

class SharpEntityListTestRequiredFilter extends EntityListSelectRequiredFilter
{
    public function values(): array
    {
        return [1 => 'A', 2 => 'B'];
    }

    public function defaultValue(): mixed
    {
        return 2;
    }
}

class SharpEntityListDateRangeTestFilter extends EntityListDateRangeFilter
{
}

class SharpEntityListDateRangeRequiredTestFilter extends \Code16\Sharp\EntityList\Filters\EntityListDateRangeRequiredFilter
{
    public function defaultValue(): array
    {
        return ['start' => Carbon::now()->subDay(), 'end' => Carbon::now()];
    }
}

class SharpEntityListTestCheckFilter extends \Code16\Sharp\EntityList\Filters\EntityListCheckFilter
{
}
