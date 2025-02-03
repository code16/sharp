<?php

use Code16\Sharp\EntityList\Filters\EntityListSelectFilter;
use Code16\Sharp\EntityList\Filters\EntityListSelectMultipleFilter;
use Code16\Sharp\EntityList\Filters\EntityListSelectRequiredFilter;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonList;
use Illuminate\Contracts\Support\Arrayable;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    sharp()->config()->addEntity('person', PersonEntity::class);
    login();
});

it('filters instances of an entity list', function () {
    $this->withoutExceptionHandling();
    fakeListFor('person', new class() extends PersonList
    {
        protected function getFilters(): ?array
        {
            return [
                new class() extends EntityListSelectFilter
                {
                    public function buildFilterConfig(): void
                    {
                        $this->configureKey('job');
                    }

                    public function values(): array
                    {
                        return [
                            'physicist' => 'Physicist',
                            'physician' => 'Physician',
                        ];
                    }
                },
            ];
        }

        public function getListData(): array|Arrayable
        {
            return collect([
                ['id' => 1, 'name' => 'Marie Curie', 'job' => 'physicist'],
                ['id' => 2, 'name' => 'Louis Pasteur', 'job' => 'physician'],
            ])
                ->filter(fn ($item) => $item['job'] == $this->queryParams->filterFor('job'))
                ->values();
        }
    });

    $this
        ->get('/sharp/s-list/person?filter_job=physicist')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('entityList.data', [
                ['id' => 1, 'name' => 'Marie Curie'],
            ])
        );
});

it('uses the default value of a required filter if no value was sent', function () {
    fakeListFor('person', new class() extends PersonList
    {
        protected function getFilters(): ?array
        {
            return [
                new class() extends EntityListSelectRequiredFilter
                {
                    public function buildFilterConfig(): void
                    {
                        $this->configureKey('job');
                    }

                    public function values(): array
                    {
                        return [
                            'physicist' => 'Physicist',
                            'physician' => 'Physician',
                        ];
                    }

                    public function defaultValue(): mixed
                    {
                        return 'physicist';
                    }
                },
            ];
        }

        public function getListData(): array|Arrayable
        {
            return collect([
                ['id' => 1, 'name' => 'Marie Curie', 'job' => 'physicist'],
                ['id' => 2, 'name' => 'Louis Pasteur', 'job' => 'physician'],
            ])
                ->filter(fn ($item) => $item['job'] == $this->queryParams->filterFor('job'))
                ->values();
        }
    });

    $this
        ->get('/sharp/s-list/person')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('entityList.data', [
                ['id' => 1, 'name' => 'Marie Curie'],
            ])
        );
});

it('handles multiple filter values', function () {
    $this->withoutExceptionHandling();
    fakeListFor('person', new class() extends PersonList
    {
        protected function getFilters(): ?array
        {
            return [
                new class() extends EntityListSelectMultipleFilter
                {
                    public function buildFilterConfig(): void
                    {
                        $this->configureKey('job');
                    }

                    public function values(): array
                    {
                        return [
                            'physicist' => 'Physicist',
                            'physician' => 'Physician',
                        ];
                    }
                },
            ];
        }

        public function getListData(): array|Arrayable
        {
            return collect([
                ['id' => 1, 'name' => 'Marie Curie', 'job' => 'physicist'],
                ['id' => 2, 'name' => 'Louis Pasteur', 'job' => 'physician'],
            ])
                ->filter(fn ($item) => in_array($item['job'], $this->queryParams->filterFor('job')))
                ->values();
        }
    });

    $this
        ->get('/sharp/s-list/person?filter_job=physicist,physician')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('entityList.data', [
                ['id' => 1, 'name' => 'Marie Curie'],
                ['id' => 2, 'name' => 'Louis Pasteur'],
            ])
        );
});

it('saves retained filters in the session when set', function () {
    fakeListFor('person', new class() extends PersonList
    {
        protected function getFilters(): ?array
        {
            return [
                new class() extends EntityListSelectFilter
                {
                    public function buildFilterConfig(): void
                    {
                        $this->configureKey('job')
                            ->configureRetainInSession();
                    }

                    public function values(): array
                    {
                        return [
                            'physicist' => 'Physicist',
                            'physician' => 'Physician',
                        ];
                    }
                },
            ];
        }

        public function getListData(): array|Arrayable
        {
            return collect([
                ['id' => 1, 'name' => 'Marie Curie', 'job' => 'physicist'],
                ['id' => 2, 'name' => 'Louis Pasteur', 'job' => 'physician'],
            ])
                ->when($this->queryParams->filterFor('job'), function ($items, $job) {
                    return $items->filter(fn ($item) => $item['job'] == $job);
                })
                ->values();
        }
    });

    expect(session()->all())->not->toHaveKey('_sharp_retained_filter_job');

    $this
        ->withoutExceptionHandling()
        ->post(route('code16.sharp.list.filters.store', ['entityKey' => 'person']), [
            'filterValues' => [
                'job' => 'physicist',
            ],
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/sharp/s-list/person?filter_job=physicist');

    expect(session('_sharp_retained_filter_job'))->toBe('physicist');

    // Second call: filter should be valued
    $this
        ->get('/sharp/s-list/person')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('entityList.data', [
                ['id' => 1, 'name' => 'Marie Curie'],
            ])
        );

    // Third call: should use QS instead of session
    $this
        ->get('/sharp/s-list/person?filter_job=physician')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('entityList.data', [
                ['id' => 2, 'name' => 'Louis Pasteur'],
            ])
        );

    // reset retained filter value
    $this
        ->post(route('code16.sharp.list.filters.store', ['entityKey' => 'person']), [
            'filterValues' => [
                'job' => null,
            ],
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/sharp/s-list/person');

    $this
        ->get('/sharp/s-list/person')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('entityList.data', [
                ['id' => 1, 'name' => 'Marie Curie'],
                ['id' => 2, 'name' => 'Louis Pasteur'],
            ])
        );
});

it('handles retained multiple filter', function () {
    fakeListFor('person', new class() extends PersonList
    {
        protected function getFilters(): ?array
        {
            return [
                new class() extends EntityListSelectMultipleFilter
                {
                    public function buildFilterConfig(): void
                    {
                        $this->configureKey('job')
                            ->configureRetainInSession();
                    }

                    public function values(): array
                    {
                        return [
                            'physicist' => 'Physicist',
                            'physician' => 'Physician',
                        ];
                    }
                },
            ];
        }
    });

    expect(session()->all())->not->toHaveKey('_sharp_retained_filter_job');

    $this
        ->post(route('code16.sharp.list.filters.store', ['entityKey' => 'person']), [
            'filterValues' => [
                'job' => ['physicist', 'physician'],
            ],
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/sharp/s-list/person?filter_job='.urlencode('physicist,physician'));

    // Call to retain the filter on session
    $this
        ->get('/sharp/s-list/person?filter_job=physicist,physician')
        ->assertOk();

    expect(session('_sharp_retained_filter_job'))->toBe('physicist,physician');
});

it('handles retained required filter', function () {
    fakeListFor('person', new class() extends PersonList
    {
        protected function getFilters(): ?array
        {
            return [
                new class() extends EntityListSelectRequiredFilter
                {
                    public function buildFilterConfig(): void
                    {
                        $this->configureKey('job')
                            ->configureRetainInSession();
                    }

                    public function values(): array
                    {
                        return [
                            'physicist' => 'Physicist',
                            'physician' => 'Physician',
                        ];
                    }

                    public function defaultValue(): mixed
                    {
                        return 'physicist';
                    }
                },
            ];
        }

        public function getListData(): array|Arrayable
        {
            return collect([
                ['id' => 1, 'name' => 'Marie Curie', 'job' => 'physicist'],
                ['id' => 2, 'name' => 'Louis Pasteur', 'job' => 'physician'],
            ])
                ->when($this->queryParams->filterFor('job'), function ($items, $job) {
                    return $items->filter(fn ($item) => $item['job'] == $job);
                })
                ->values();
        }
    });

    // First call: use default value
    $this
        ->get('/sharp/s-list/person')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('entityList.data', [
                ['id' => 1, 'name' => 'Marie Curie'],
            ])
        );

    $this
        ->post(route('code16.sharp.list.filters.store', ['entityKey' => 'person']), [
            'filterValues' => [
                'job' => 'physician',
            ],
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/sharp/s-list/person?filter_job=physician');

    // Second call: use filter value
    $this
        ->get('/sharp/s-list/person?filter_job=physician')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('entityList.data', [
                ['id' => 2, 'name' => 'Louis Pasteur'],
            ])
        );

    // Third call: no filter, use retained value
    $this
        ->get('/sharp/s-list/person')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('entityList.data', [
                ['id' => 2, 'name' => 'Louis Pasteur'],
            ])
        );

    $this
        ->post(route('code16.sharp.list.filters.store', ['entityKey' => 'person']), [
            'filterValues' => [
                'job' => null,
            ],
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/sharp/s-list/person');

    // Fourth call: reset retained value
    $this
        ->get('/sharp/s-list/person')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('entityList.data', [
                ['id' => 1, 'name' => 'Marie Curie'],
            ])
        );
});

it('fakes request segments to fix the breadcrumb in case it is built', function () {
    fakeListFor('person', new class() extends PersonList
    {
        protected function getFilters(): ?array
        {
            return [
                new class() extends EntityListSelectFilter
                {
                    public function buildFilterConfig(): void
                    {
                        // Build breadcrumb
                        sharp()->context()->breadcrumb()->getCurrentSegmentUrl();

                        $this->configureKey('job');
                    }

                    public function values(): array
                    {
                        return [
                            'physicist' => 'Physicist',
                        ];
                    }
                },
            ];
        }
    });

    $this
        ->post(route('code16.sharp.list.filters.store', ['entityKey' => 'person']), [
            'filterValues' => [
                'job' => 'physicist',
            ],
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/sharp/s-list/person?filter_job=physicist');
});
