<?php

use Carbon\Carbon;
use Code16\Sharp\EntityList\Filters\EntityListCheckFilter;
use Code16\Sharp\EntityList\Filters\EntityListDateRangeFilter;
use Code16\Sharp\EntityList\Filters\EntityListDateRangeRequiredFilter;
use Code16\Sharp\EntityList\Filters\EntityListSelectFilter;
use Code16\Sharp\EntityList\Filters\EntityListSelectMultipleFilter;
use Code16\Sharp\EntityList\Filters\EntityListSelectRequiredFilter;
use Code16\Sharp\EntityList\Filters\HiddenFilter;
use Code16\Sharp\Tests\Unit\EntityList\Fakes\FakeSharpEntityList;

it('allows to configure filters with a key', function () {
    $list = new class() extends FakeSharpEntityList
    {
        public function getFilters(): array
        {
            return [
                new class() extends EntityListSelectFilter
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

    expect($list->listConfig()['filters'])
        ->toEqual([
            '_root' => [
                [
                    'key' => 'test',
                    'label' => 'test_label',
                    'multiple' => false,
                    'required' => false,
                    'values' => [
                        ['id' => 1, 'label' => 'A'],
                        ['id' => 2, 'label' => 'B'],
                    ],
                    'type' => 'select',
                    'master' => false,
                    'searchable' => false,
                    'searchKeys' => ['label'],
                ],
            ],
        ]);
});

it('allows to configure filters without a key', function () {
    $list = new class() extends FakeSharpEntityList
    {
        public function getFilters(): array
        {
            return [
                new class() extends EntityListSelectFilter
                {
                    public function buildFilterConfig(): void {}

                    public function values(): array
                    {
                        return [1 => 'A', 2 => 'B'];
                    }
                },
            ];
        }
    };

    $list->buildListConfig();

    expect($list->listConfig()['filters']['_root'][0]['key'])
        ->toEqual(class_basename(get_class($list->getFilters()[0])));
});

it('allows to configure filters with hidden filters', function () {
    $list = new class() extends FakeSharpEntityList
    {
        public function getFilters(): array
        {
            return [
                HiddenFilter::make('test'),
                new class() extends EntityListSelectFilter
                {
                    public function buildFilterConfig(): void {}

                    public function values(): array
                    {
                        return [1 => 'A', 2 => 'B'];
                    }
                },
            ];
        }
    };

    $list->buildListConfig();

    expect($list->listConfig()['filters']['_root'][0]['key'])
        ->toEqual(class_basename(get_class($list->getFilters()[1])));
});

it('allows section based filters config', function () {
    $list = new class() extends FakeSharpEntityList
    {
        public function getFilters(): ?array
        {
            return [
                new class() extends EntityListSelectFilter
                {
                    public function values(): array
                    {
                        return [1 => 'A', 2 => 'B'];
                    }
                },
                'section-1' => [
                    new class() extends EntityListSelectFilter
                    {
                        public function values(): array
                        {
                            return [3 => 'C', 4 => 'D'];
                        }
                    },
                ],
            ];
        }
    };

    $list->buildListConfig();

    expect($list->listConfig()['filters']['_root'][0]['values'])
        ->toEqual([
            ['id' => 1, 'label' => 'A'],
            ['id' => 2, 'label' => 'B'],
        ])
        ->and($list->listConfig()['filters']['section-1'][0]['values'])
        ->toEqual([
            ['id' => 3, 'label' => 'C'],
            ['id' => 4, 'label' => 'D'],
        ]);
});

it('allows list filter to be multiple', function () {
    $list = new class() extends FakeSharpEntityList
    {
        public function getFilters(): array
        {
            return [
                new class() extends EntityListSelectMultipleFilter
                {
                    public function values(): array
                    {
                        return [1 => 'A', 2 => 'B'];
                    }
                },
            ];
        }
    };

    $list->buildListConfig();

    expect($list->listConfig()['filters']['_root'][0]['multiple'])->toBeTrue()
        ->and($list->listConfig()['filters']['_root'][0]['required'])->toBeFalse();
});

it('allows list filter to be required', function () {
    $list = new class() extends FakeSharpEntityList
    {
        public function getFilters(): array
        {
            return [
                new class() extends EntityListSelectRequiredFilter
                {
                    public function buildFilterConfig(): void
                    {
                        $this->configureKey('test');
                    }

                    public function values(): array
                    {
                        return ['A' => 'A', 'B' => 'B'];
                    }

                    public function defaultValue(): mixed
                    {
                        return 'B';
                    }
                },
            ];
        }
    };

    $list->buildListConfig();

    expect($list->listConfig()['filters']['_root'][0]['required'])->toBeTrue()
        ->and($list->filterContainer()->getCurrentFilterValuesForFront(null))->toEqual([
            'default' => ['test' => 'B'],
            'current' => ['test' => 'B'],
            'valuated' => ['test' => false],
        ]);
});

it('allows to define a label for the filter', function () {
    $list = new class() extends FakeSharpEntityList
    {
        public function getFilters(): array
        {
            return [
                new class() extends EntityListSelectFilter
                {
                    public function buildFilterConfig(): void
                    {
                        $this->configureLabel('test label');
                    }

                    public function values(): array
                    {
                        return [];
                    }
                },
            ];
        }
    };

    $list->buildListConfig();

    expect($list->listConfig()['filters']['_root'][0]['label'])->toEqual('test label');
});

it('allows to declare a filter as master', function () {
    $list = new class() extends FakeSharpEntityList
    {
        public function getFilters(): array
        {
            return [
                new class() extends EntityListSelectFilter
                {
                    public function buildFilterConfig(): void
                    {
                        $this->configureMaster();
                    }

                    public function values(): array
                    {
                        return [];
                    }
                },
            ];
        }
    };

    $list->buildListConfig();

    expect($list->listConfig()['filters']['_root'][0]['master'])->toBeTrue();
});

it('allows to declare a filter as searchable', function () {
    $list = new class() extends FakeSharpEntityList
    {
        public function getFilters(): array
        {
            return [
                new class() extends EntityListSelectFilter
                {
                    public function buildFilterConfig(): void
                    {
                        $this->configureSearchable();
                    }

                    public function values(): array
                    {
                        return [];
                    }
                },
            ];
        }
    };

    $list->buildListConfig();

    expect($list->listConfig()['filters']['_root'][0]['searchable'])->toBeTrue()
        ->and($list->listConfig()['filters']['_root'][0]['searchKeys'])->toEqual(['label']);
});

it('allows to define searchKeys on a filter', function () {
    $list = new class() extends FakeSharpEntityList
    {
        public function getFilters(): array
        {
            return [
                new class() extends EntityListSelectFilter
                {
                    public function buildFilterConfig(): void
                    {
                        $this->configureSearchable()
                            ->configureSearchKeys(['a', 'b']);
                    }

                    public function values(): array
                    {
                        return [];
                    }
                },
            ];
        }
    };

    $list->buildListConfig();

    expect($list->listConfig()['filters']['_root'][0]['searchKeys'])->toEqual(['a', 'b']);
});

it('allows to declare a filter as retained and to set its default value', function () {
    $list = new class() extends FakeSharpEntityList
    {
        public function getFilters(): array
        {
            return [
                new class() extends EntityListSelectFilter
                {
                    public function buildFilterConfig(): void
                    {
                        $this->configureKey('test_20')
                            ->configureRetainInSession();
                    }

                    public function values(): array
                    {
                        return [];
                    }
                },
            ];
        }
    };

    // Artificially put retained value in session
    session()->put('_sharp_retained_filter_test_20', 2);
    $list->buildListConfig();

    expect($list->filterContainer()->getCurrentFilterValuesForFront(null))->toEqual([
        'default' => ['test_20' => null],
        'current' => ['test_20' => 2],
        'valuated' => ['test_20' => true],
    ]);
});

it('returns retained value for required and retained filters returns by default', function () {
    $list = new class() extends FakeSharpEntityList
    {
        public function getFilters(): array
        {
            return [
                new class() extends EntityListSelectRequiredFilter
                {
                    public function buildFilterConfig(): void
                    {
                        $this->configureKey('test_21')
                            ->configureRetainInSession();
                    }

                    public function values(): array
                    {
                        return [1 => 'A', 2 => 'B'];
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

    expect($list->filterContainer()->getCurrentFilterValuesForFront(null))->toEqual([
        'default' => ['test_21' => 1],
        'current' => ['test_21' => 2],
        'valuated' => ['test_21' => true],
    ]);
});

it('formats date range filter retained value', function () {
    $list = new class() extends FakeSharpEntityList
    {
        public function getFilters(): array
        {
            return [
                new class() extends EntityListDateRangeFilter
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

    expect($list->filterContainer()->getCurrentFilterValuesForFront(null))->toEqual([
        'default' => ['test_22' => null],
        'current' => [
            'test_22' => [
                'start' => '2019-09-22',
                'end' => '2019-09-25',
                'preset' => null,
                'formatted' => [
                    'start' => '2019-09-22',
                    'end' => '2019-09-25',
                ],
            ],
        ],
        'valuated' => ['test_22' => true],
    ]);
});

it('allows to declare a date range filter as required', function () {
    $list = new class() extends FakeSharpEntityList
    {
        public function getFilters(): array
        {
            return [
                new class() extends EntityListDateRangeRequiredFilter
                {
                    public function buildFilterConfig(): void
                    {
                        $this->configureKey('test');
                    }

                    public function defaultValue(): array
                    {
                        return ['start' => Carbon::now()->subDay(), 'end' => Carbon::now()];
                    }
                },
            ];
        }
    };

    $list->buildListConfig();

    expect($list->listConfig()['filters']['_root'][0]['required'])->toBeTrue()
        ->and($list->filterContainer()->getCurrentFilterValuesForFront(null))->toEqual([
            'default' => [
                'test' => [
                    'start' => Carbon::now()->subDay()->format('Y-m-d'),
                    'end' => Carbon::now()->format('Y-m-d'),
                    'preset' => null,
                    'formatted' => [
                        'start' => Carbon::now()->subDay()->format('Y-m-d'),
                        'end' => Carbon::now()->format('Y-m-d'),
                    ],
                ],
            ],
            'current' => [
                'test' => [
                    'start' => Carbon::now()->subDay()->format('Y-m-d'),
                    'end' => Carbon::now()->format('Y-m-d'),
                    'preset' => null,
                    'formatted' => [
                        'start' => Carbon::now()->subDay()->format('Y-m-d'),
                        'end' => Carbon::now()->format('Y-m-d'),
                    ],
                ],
            ],
            'valuated' => ['test' => false],
        ]);
});

it('allows to define a date display format for a date range filter', function () {
    $list = new class() extends FakeSharpEntityList
    {
        public function getFilters(): array
        {
            return [
                new class() extends EntityListDateRangeFilter
                {
                    public function buildFilterConfig(): void
                    {
                        $this->configureKey('test_22')
                            ->configureDateFormat('YYYY_MM_DD')
                            ->configureRetainInSession();
                    }
                },
            ];
        }
    };

    // Artificially put retained value in session
    session()->put('_sharp_retained_filter_test_22', '20190922..20190925');
    $list->buildListConfig();

    expect($list->filterContainer()->getCurrentFilterValuesForFront(null))->toEqual([
        'default' => ['test_22' => null],
        'current' => [
            'test_22' => [
                'start' => '2019-09-22',
                'end' => '2019-09-25',
                'preset' => null,
                'formatted' => [
                    'start' => '2019_09_22',
                    'end' => '2019_09_25',
                ],
            ],
        ],
        'valuated' => ['test_22' => true],
    ]);
});

it('allows to define the monday first attribute for a date range filter', function () {
    $list = new class() extends FakeSharpEntityList
    {
        public function getFilters(): array
        {
            return [
                new class() extends EntityListDateRangeFilter
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

    expect($list->listConfig()['filters']['_root'][0]['mondayFirst'])->toBeFalse();
});

it('allows to define a check filter', function () {
    $list = new class() extends FakeSharpEntityList
    {
        public function getFilters(): array
        {
            return [
                new class() extends EntityListCheckFilter {},
            ];
        }
    };

    $list->buildListConfig();

    expect($list->listConfig()['filters']['_root'][0]['type'])->toEqual('check');
});

it('allows to drop a filter afterwards', function () {
    $list = new class() extends FakeSharpEntityList
    {
        public function getFilters(): array
        {
            return [
                new class() extends EntityListSelectFilter
                {
                    public function values(): array
                    {
                        return [];
                    }
                },
                new class() extends EntityListSelectFilter
                {
                    public function buildFilterConfig(): void
                    {
                        $this->configureKey('test');
                    }

                    public function values(): array
                    {
                        return [];
                    }
                },
            ];
        }

        public function getListData(): array
        {
            $this->hideFilter('test');

            return [];
        }
    };

    $list->buildListConfig();
    expect($list->listConfig()['filters']['_root'])->toHaveCount(2);

    $list->data();
    expect($list->listConfig()['filters']['_root'])->toHaveCount(1);
});
