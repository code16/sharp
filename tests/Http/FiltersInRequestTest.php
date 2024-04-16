<?php

use Code16\Sharp\EntityList\Filters\EntityListSelectFilter;
use Code16\Sharp\EntityList\Filters\EntityListSelectMultipleFilter;
use Code16\Sharp\EntityList\Filters\EntityListSelectRequiredFilter;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonList;
use Illuminate\Contracts\Support\Arrayable;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    sharpConfig()->addEntity('person', PersonEntity::class);
    login();
});

it('filters instances of an entity list', function () {
    fakeListFor('person', new class extends PersonList
    {
        protected function getFilters(): ?array
        {
            return [
                new class extends EntityListSelectFilter
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
    fakeListFor('person', new class extends PersonList
    {
        protected function getFilters(): ?array
        {
            return [
                new class extends EntityListSelectRequiredFilter
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
    fakeListFor('person', new class extends PersonList
    {
        protected function getFilters(): ?array
        {
            return [
                new class extends EntityListSelectMultipleFilter
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
    fakeListFor('person', new class extends PersonList
    {
        protected function getFilters(): ?array
        {
            return [
                new class extends EntityListSelectFilter
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

    // Call to retain the filter on session
    $this
        ->get('/sharp/s-list/person?filter_job=physicist')
        ->assertOk();

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

    // Third call: update retained filter value
    $this
        ->get('/sharp/s-list/person?filter_job=physician')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('entityList.data', [
                ['id' => 2, 'name' => 'Louis Pasteur'],
            ])
        );

    // Fourth call: reset retained filter value
    $this
        ->get('/sharp/s-list/person?filter_job=')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('entityList.data', [
                ['id' => 1, 'name' => 'Marie Curie'],
                ['id' => 2, 'name' => 'Louis Pasteur'],
            ])
        );
});

it('handles retained multiple filter', function () {
    fakeListFor('person', new class extends PersonList
    {
        protected function getFilters(): ?array
        {
            return [
                new class extends EntityListSelectMultipleFilter
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

    // Call to retain the filter on session
    $this
        ->get('/sharp/s-list/person?filter_job=physicist,physician')
        ->assertOk();

    expect(session('_sharp_retained_filter_job'))->toBe('physicist,physician');
});

it('handles retained required filter', function () {
    fakeListFor('person', new class extends PersonList
    {
        protected function getFilters(): ?array
        {
            return [
                new class extends EntityListSelectRequiredFilter
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

    // Fourth call: reset retained value
    $this
        ->get('/sharp/s-list/person?filter_job=')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('entityList.data', [
                ['id' => 1, 'name' => 'Marie Curie'],
            ])
        );
});
