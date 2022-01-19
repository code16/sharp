<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\EntityList\Filters\EntityListSelectFilter;
use Code16\Sharp\EntityList\Filters\EntityListSelectMultipleFilter;
use Code16\Sharp\EntityList\Filters\EntityListSelectRequiredFilter;
use Code16\Sharp\Tests\Fixtures\PersonSharpEntityList;
use Code16\Sharp\Tests\Fixtures\PersonSharpEntityListAgeFilter;
use Code16\Sharp\Tests\Fixtures\PersonSharpEntityListAgeMultipleFilter;
use Illuminate\Contracts\Support\Arrayable;

class FiltersInRequestTest extends BaseApiTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->login();
    }

    /** @test */
    public function we_can_filter_instances_of_an_entity_list()
    {
        $this->buildTheWorld();

        $this->json('get', '/sharp/api/list/person?filter_'.class_basename(PersonSharpEntityListAgeFilter::class).'=22')
            ->assertStatus(200)
            ->assertJsonFragment([
                'data' => [
                    'list' => [
                        'items' => [
                            ['id' => 1, 'name' => 'John <b>Wayne</b>', 'age' => 22],
                        ],
                    ],
                ],
            ]);
    }

    /** @test */
    public function default_filter_value_is_used_if_no_value_was_sent()
    {
        $this->buildTheWorld();

        // We use a special QS key "default_age" only for test purpose
        // to know that we should use default value in this case
        $this->json('get', '/sharp/api/list/person?default_age=true')
            ->assertStatus(200)
            ->assertJsonFragment([
                'data' => [
                    'list' => [
                        'items' => [
                            ['id' => 1, 'name' => 'John <b>Wayne</b>', 'age' => 22],
                        ],
                    ],
                ],
            ]);
    }

    /** @test */
    public function we_can_filter_with_multiple_values_on_entities()
    {
        $this->buildTheWorld();

        $this->json('get', '/sharp/api/list/person?filter_age_multiple=22,26')
            ->assertStatus(200)
            ->assertJsonFragment([
                'data' => [
                    'list' => [
                        'items' => [
                            ['id' => 1, 'name' => 'John <b>Wayne</b>', 'age' => 22],
                            ['id' => 2, 'name' => 'Mary <b>Wayne</b>', 'age' => 26],
                        ],
                    ],
                ],
            ]);
    }

    /** @test */
    public function we_can_filter_on_a_single_value_with_a_multiple_values_filter()
    {
        $this->buildTheWorld();

        $this->json('get', '/sharp/api/list/person?filter_'.class_basename(PersonSharpEntityListAgeMultipleFilter::class).'=22')
            ->assertStatus(200)
            ->assertJsonFragment([
                'data' => [
                    'list' => [
                        'items' => [
                            ['id' => 1, 'name' => 'John <b>Wayne</b>', 'age' => 22],
                        ],
                    ],
                ],
            ]);
    }

    /** @test */
    public function retained_filters_are_saved_in_the_session()
    {
        app()->bind(
            PersonSharpEntityList::class,
            function () {
                return new class() extends PersonSharpEntityList
                {
                    public function getFilters(): array
                    {
                        return [
                            FiltersInRequestTestRetainedActiveFilter::class,
                        ];
                    }
                };
            },
        );

        $this->buildTheWorld();
        $key = (new FiltersInRequestTestRetainedActiveFilter())->getKey();

        $this->assertFalse((bool) session("_sharp_retained_filter_$key"));

        // Call to retain the filter on session
        $this->getJson("/sharp/api/list/person?filter_$key=1");

        $this->assertTrue((bool) session("_sharp_retained_filter_$key"));
    }

    /** @test */
    public function retained_filter_values_are_retrieved_from_the_session()
    {
        $this->withoutExceptionHandling();
        app()->bind(
            PersonSharpEntityList::class,
            function () {
                return new class() extends PersonSharpEntityList
                {
                    public function getListData(): array|Arrayable
                    {
                        $items = [
                            ['id' => 1, 'name' => 'John', 'age' => 30, 'active' => true],
                            ['id' => 2, 'name' => 'Baby', 'age' => 2, 'active' => false],
                        ];

                        if ($this->queryParams->filterFor(FiltersInRequestTestRetainedActiveFilter::class) !== null) {
                            $items = collect($items)
                                ->filter(function ($item) {
                                    return $item['active'] == $this->queryParams->filterFor(FiltersInRequestTestRetainedActiveFilter::class);
                                })
                                ->values();
                        }

                        return $this->transform($items);
                    }

                    public function getFilters(): ?array
                    {
                        return [
                            FiltersInRequestTestRetainedActiveFilter::class,
                        ];
                    }
                };
            },
        );

        $this->buildTheWorld();

        // First call to retain the filter on session
        $this->json('get', '/sharp/api/list/person?filter_'.class_basename(FiltersInRequestTestRetainedActiveFilter::class).'=1');

        // Second call: filter should be valued to 1
        $this->json('get', '/sharp/api/list/person')
            ->assertStatus(200)
            ->assertJsonFragment([
                'data' => [
                    'list' => [
                        'items' => [
                            ['id' => 1, 'name' => 'John', 'age' => 30],
                        ],
                    ],
                ],
            ]);

        // Third call, change filter value
        $this->json('get', '/sharp/api/list/person?filter_'.class_basename(FiltersInRequestTestRetainedActiveFilter::class).'=0')
            ->assertStatus(200)
            ->assertJsonFragment([
                'items' => [
                    ['id' => 2, 'name' => 'Baby', 'age' => 2],
                ],
            ]);

        // Fourth call, reset filter value (un-required filters)
        $this->json('get', '/sharp/api/list/person?filter_'.class_basename(FiltersInRequestTestRetainedActiveFilter::class).'=')
            ->assertStatus(200)
            ->assertJsonFragment([
                'items' => [
                    ['id' => 1, 'name' => 'John', 'age' => 30],
                    ['id' => 2, 'name' => 'Baby', 'age' => 2],
                ],
            ]);
    }

    /** @test */
    public function retained_filter_works_with_multiple_filter()
    {
        app()->bind(
            PersonSharpEntityList::class,
            function () {
                return new class() extends PersonSharpEntityList
                {
                    public function getListData(): array|Arrayable
                    {
                        $items = [
                            ['id' => 1, 'name' => 'John', 'age' => 30],
                            ['id' => 2, 'name' => 'Mary', 'age' => 32],
                            ['id' => 3, 'name' => 'Baby', 'age' => 2],
                        ];

                        if ($age = $this->queryParams->filterFor(FiltersInRequestTestRetainedAgeMultipleFilter::class)) {
                            $items = collect($items)
                                ->whereIn('age', $age)
                                ->values();
                        }

                        return $this->transform($items);
                    }

                    public function getFilters(): ?array
                    {
                        return [
                            FiltersInRequestTestRetainedAgeMultipleFilter::class,
                        ];
                    }
                };
            },
        );

        $this->buildTheWorld();
        $key = (new FiltersInRequestTestRetainedAgeMultipleFilter)->getKey();

        // First call to retain the filter on session
        $this->getJson("/sharp/api/list/person?filter_$key=30,32");

        // Second call: filter should be valued
        $this->getJson('/sharp/api/list/person')
            ->assertOk()
            ->assertJsonFragment([
                'items' => [
                    ['id' => 1, 'name' => 'John', 'age' => 30],
                    ['id' => 2, 'name' => 'Mary', 'age' => 32],
                ],
            ]);

        // Third call: filter should be reset
        $this->getJson("/sharp/api/list/person?filter_$key=")
            ->assertOk()
            ->assertJsonFragment([
                'items' => [
                    ['id' => 1, 'name' => 'John', 'age' => 30],
                    ['id' => 2, 'name' => 'Mary', 'age' => 32],
                    ['id' => 3, 'name' => 'Baby', 'age' => 2],
                ],
            ]);
    }

    /** @test */
    public function retained_filter_works_with_required_filter()
    {
        app()->bind(
            PersonSharpEntityList::class,
            function () {
                return new class() extends PersonSharpEntityList
                {
                    public function getListData(): array|Arrayable
                    {
                        $items = [
                            ['id' => 1, 'name' => 'John', 'age' => 30],
                            ['id' => 2, 'name' => 'Mary', 'age' => 32],
                            ['id' => 3, 'name' => 'Baby', 'age' => 2],
                        ];

                        $items = collect($items)
                            ->where('age', $this->queryParams->filterFor(FiltersInRequestTestRetainedAgeRequiredFilter::class))
                            ->values();

                        return $this->transform($items);
                    }

                    public function getFilters(): ?array
                    {
                        return [
                            FiltersInRequestTestRetainedAgeRequiredFilter::class,
                        ];
                    }
                };
            },
        );

        $this->buildTheWorld();
        $key = (new FiltersInRequestTestRetainedAgeRequiredFilter)->getKey();

        // First call to retain the filter on session (default is 2)
        $this->getJson("/sharp/api/list/person?filter_$key=30");

        // Second call: filter should be valued to 30
        $this->getJson('/sharp/api/list/person')
            ->assertOk()
            ->assertJsonFragment([
                'items' => [
                    ['id' => 1, 'name' => 'John', 'age' => 30],
                ],
            ]);
    }
}

class FiltersInRequestTestRetainedActiveFilter extends EntityListSelectFilter
{
    public function buildFilterConfig(): void
    {
        $this->configureRetainInSession();
    }

    public function values(): array
    {
        return [0, 1];
    }
}

class FiltersInRequestTestRetainedAgeMultipleFilter extends EntityListSelectMultipleFilter
{
    public function buildFilterConfig(): void
    {
        $this->configureRetainInSession();
    }

    public function values(): array
    {
        return range(0, 80);
    }
}

class FiltersInRequestTestRetainedAgeRequiredFilter extends EntityListSelectRequiredFilter
{
    public function buildFilterConfig(): void
    {
        $this->configureRetainInSession();
    }

    public function values(): array
    {
        return range(0, 80);
    }

    public function defaultValue(): mixed
    {
        return 2;
    }
}
