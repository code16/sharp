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
    public function we_can_get_list_filters_config_with_an_instance()
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

        $this->assertArraySubset(
            [
                'filters' => [
                    [
                        'key' => 'test',
                        'label' => 'test_label',
                        'multiple' => false,
                        'required' => false,
                        'values' => [
                            ['id' => 1, 'label' => 'A'],
                            ['id' => 2, 'label' => 'B'],
                        ],
                    ],
                ],
            ],
            $list->listConfig(),
        );
    }

    /** @test */
    public function we_can_get_list_filters_config_with_a_class_name()
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

        $this->assertArraySubset(
            [
                'filters' => [
                    [
                        'key' => class_basename(SharpEntityListTestFilter::class),
                        'multiple' => false,
                        'required' => false,
                        'values' => [
                            ['id' => 1, 'label' => 'A'],
                            ['id' => 2, 'label' => 'B'],
                        ],
                    ],
                ],
            ],
            $list->listConfig(),
        );
    }

    /** @test */
    public function a_list_filters_can_be_multiple()
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

        $this->assertArraySubset(
            [
                'filters' => [
                    [
                        'key' => class_basename(SharpEntityListTestMultipleFilter::class),
                        'multiple' => true,
                        'required' => false,
                    ],
                ],
            ],
            $list->listConfig(),
        );
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

        $this->assertArraySubset(
            [
                'filters' => [
                    [
                        'key' => class_basename(SharpEntityListTestRequiredFilter::class),
                        'multiple' => false,
                        'required' => true,
                        'values' => [
                            ['id' => 1, 'label' => 'A'],
                            ['id' => 2, 'label' => 'B'],
                        ],
                        'default' => 2,
                    ],
                ],
            ],
            $list->listConfig(),
        );
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
                            $this->configureKey('test')
                                ->configureLabel('test label');
                        }
                    },
                ];
            }
        };

        $list->buildListConfig();

        $this->assertArraySubset(
            [
                'filters' => [
                    [
                        'key' => 'test',
                        'label' => 'test label',
                    ],
                ],
            ],
            $list->listConfig(),
        );
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
                            $this->configureKey('test')
                                ->configureMaster(true);
                        }
                    },
                ];
            }
        };

        $list->buildListConfig();

        $this->assertArraySubset(
            [
                'filters' => [
                    [
                        'key' => 'test',
                        'master' => true,
                    ],
                ],
            ],
            $list->listConfig(),
        );
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
                            $this->configureKey('test')
                                ->configureSearchable();
                        }
                    },
                ];
            }
        };

        $list->buildListConfig();

        $this->assertArraySubset(
            [
                'filters' => [
                    [
                        'key' => 'test',
                        'searchable' => true,
                        'searchKeys' => ['label'],
                    ],
                ],
            ],
            $list->listConfig(),
        );
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
                            $this->configureKey('test')
                                ->configureSearchable()
                                ->configureSearchKeys(['a', 'b']);
                        }
                    },
                ];
            }
        };

        $list->buildListConfig();

        $this->assertArraySubset(
            [
                'filters' => [
                    [
                        'key' => 'test',
                        'searchable' => true,
                        'searchKeys' => ['a', 'b'],
                    ],
                ],
            ],
            $list->listConfig(),
        );
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
                            $this->configureKey('test')
                                ->configureTemplate('{{letter}} {{maj}}');
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

        $this->assertArraySubset(
            [
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
            ],
            $list->listConfig(),
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

        $this->assertArraySubset(
            [
                'filters' => [
                    [
                        'key' => 'test_20',
                        'default' => 2,
                    ],
                ],
            ],
            $list->listConfig(),
        );
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

        $this->assertArraySubset(
            [
                'filters' => [
                    [
                        'key' => 'test_21',
                        'default' => 2,
                    ],
                ],
            ],
            $list->listConfig(),
        );
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

        $this->assertArraySubset(
            [
                'filters' => [
                    [
                        'key' => 'test_22',
                        'default' => [
                            'start' => '2019-09-22',
                            'end' => '2019-09-25',
                        ],
                    ],
                ],
            ],
            $list->listConfig(),
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

        $this->assertArraySubset(
            [
                'filters' => [
                    [
                        'key' => class_basename(SharpEntityListDateRangeTestFilter::class),
                        'type' => 'daterange',
                        'required' => false,
                    ],
                ],
            ],
            $list->listConfig(),
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

        $this->assertArraySubset(
            [
                'filters' => [
                    [
                        'default' => [
                            'start' => Carbon::now()->subDay()->format('Y-m-d'),
                            'end' => Carbon::now()->format('Y-m-d'),
                        ],
                    ],
                ],
            ],
            $list->listConfig(),
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
                            $this->configureKey('test')
                                ->configureDateFormat('YYYY-MM-DD');
                        }
                    },
                ];
            }
        };

        $list->buildListConfig();

        $this->assertArraySubset(
            [
                'filters' => [
                    [
                        'key' => 'test',
                        'displayFormat' => 'YYYY-MM-DD',
                    ],
                ],
            ],
            $list->listConfig(),
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
                            $this->configureKey('test')
                                ->configureMondayFirst(false);
                        }
                    },
                ];
            }
        };

        $list->buildListConfig();

        $this->assertArraySubset(
            [
                'filters' => [
                    [
                        'key' => 'test',
                        'mondayFirst' => false,
                    ],
                ],
            ],
            $list->listConfig(),
        );
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

        $this->assertArraySubset(
            [
                'filters' => [
                    [
                        'key' => class_basename(SharpEntityListTestCheckFilter::class),
                        'type' => 'check',
                    ],
                ],
            ],
            $list->listConfig(),
        );
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
