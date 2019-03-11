<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\Tests\Fixtures\PersonSharpEntityList;
use Code16\Sharp\Utils\Filters\ListFilter;
use Code16\Sharp\Utils\Filters\ListMultipleFilter;
use Code16\Sharp\Utils\Filters\ListRequiredFilter;

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

        $this->json('get', '/sharp/api/list/person?filter_age=22')
            ->assertStatus(200)
            ->assertJsonFragment(["data" => [
                "items" => [
                    ["id" => 1, "name" => "John <b>Wayne</b>", "age" => 22]
                ]
            ]]);
    }

    /** @test */
    public function default_filter_value_is_used_if_no_value_was_sent()
    {
        $this->buildTheWorld();

        // We use a special QS key "default_age" only for test purpose
        // to know that we should use default value in this case
        $this->json('get', '/sharp/api/list/person?default_age=true')
            ->assertStatus(200)
            ->assertJsonFragment(["data" => [
                "items" => [
                    ["id" => 1, "name" => "John <b>Wayne</b>", "age" => 22]
                ]
            ]]);
    }

    /** @test */
    public function we_can_filter_with_multiple_values_on_entities()
    {
        $this->buildTheWorld();

        $this->json('get', '/sharp/api/list/person?filter_age_multiple=22,26')
            ->assertStatus(200)
            ->assertJsonFragment(["data" => [
                "items" => [
                    ["id" => 1, "name" => "John <b>Wayne</b>", "age" => 22],
                    ["id" => 2, "name" => "Mary <b>Wayne</b>", "age" => 26]
                ]
            ]]);
    }

    /** @test */
    public function we_can_filter_on_a_single_value_with_a_multiple_values_filter()
    {
        $this->buildTheWorld();

        $this->json('get', '/sharp/api/list/person?filter_age_multiple=22')
            ->assertStatus(200)
            ->assertJsonFragment(["data" => [
                "items" => [
                    ["id" => 1, "name" => "John <b>Wayne</b>", "age" => 22],
                ]
            ]]);
    }

    /** @test */
    public function we_can_define_a_was_set_callback_on_a_filter()
    {
        $this->buildTheWorld();

        $age = rand(1, 99);
        $this->json('get', '/sharp/api/list/person?filter_age=' . $age);

        // The age was put in session in the Callback
        $this->assertEquals($age, session("filter_age_was_set"));
    }

    /** @test */
    public function we_can_force_a_filter_value_in_a_callback()
    {
        $this->buildTheWorld();

        // Filter `age` will be force set in the `age_forced` filter callback
        $this->json('get', '/sharp/api/list/person?filter_age_forced=22&filter_age=12')
            ->assertStatus(200)
            ->assertJsonFragment(["data" => [
                "items" => [
                    ["id" => 1, "name" => "John <b>Wayne</b>", "age" => 22],
                ]
            ]]);
    }

    /** @test */
    public function retained_filters_are_saved_in_the_session()
    {
        app()->bind(
            PersonSharpEntityList::class,
            function() {
                return new class() extends PersonSharpEntityList {
                    function buildListConfig()
                    {
                        $this->addFilter(
                            "active",
                            FiltersInRequestTestRetainedActiveFilter::class
                        );
                    }
                };
            }
        );

        $this->buildTheWorld();

        $this->assertFalse(!!session("_sharp_retained_filter_active"));

        // Call to retain the filter on session
        $this->json('get', '/sharp/api/list/person?filter_active=1');

        $this->assertTrue(!!session("_sharp_retained_filter_active"));
    }

    /** @test */
    public function retained_filter_values_are_retrieved_from_the_session()
    {
        app()->bind(
            PersonSharpEntityList::class,
            function() {
                return new class() extends PersonSharpEntityList {
                    function getListData(EntityListQueryParams $params)
                    {
                        $items = [
                            ["id" => 1, "name" => "John", "age" => 30, "active" => true],
                            ["id" => 2, "name" => "Baby", "age" => 2, "active" => false],
                        ];

                        if ($params->filterFor("active") !== null) {
                            $items = collect($items)
                                ->filter(function ($item) use ($params) {
                                    return $item["active"] == $params->filterFor("active");
                                })
                                ->values();
                        }

                        return $this->transform($items);
                    }
                    function buildListConfig()
                    {
                        $this->addFilter(
                            "active",
                            FiltersInRequestTestRetainedActiveFilter::class
                        );
                    }
                };
            }
        );

        $this->buildTheWorld();

        // First call to retain the filter on session
        $this->json('get', '/sharp/api/list/person?filter_active=1');

        // Second call: filter should be valued to 1
        $this->json('get', '/sharp/api/list/person')
            ->assertStatus(200)
            ->assertJsonFragment(["data" => [
                "items" => [
                    ["id" => 1, "name" => "John", "age" => 30],
                ]
            ]]);

        // Third call, change filter value
        $this->json('get', '/sharp/api/list/person?filter_active=0')
            ->assertStatus(200)
            ->assertJsonFragment(["data" => [
                "items" => [
                    ["id" => 2, "name" => "Baby", "age" => 2],
                ]
            ]]);

        // Fourth call, reset filter value (un-required filters)
        $this->json('get', '/sharp/api/list/person?filter_active=')
            ->assertStatus(200)
            ->assertJsonFragment(["data" => [
                "items" => [
                    ["id" => 1, "name" => "John", "age" => 30],
                    ["id" => 2, "name" => "Baby", "age" => 2],
                ]
            ]]);
    }

    /** @test */
    public function retained_filter_works_with_multiple_filter()
    {
        app()->bind(
            PersonSharpEntityList::class,
            function() {
                return new class() extends PersonSharpEntityList {
                    function getListData(EntityListQueryParams $params)
                    {
                        $items = [
                            ["id" => 1, "name" => "John", "age" => 30],
                            ["id" => 2, "name" => "Mary", "age" => 32],
                            ["id" => 3, "name" => "Baby", "age" => 2],
                        ];

                        if ($params->filterFor("age")) {
                            $items = collect($items)
                                ->whereIn("age", $params->filterFor("age"))
                                ->values();
                        }

                        return $this->transform($items);
                    }
                    function buildListConfig()
                    {
                        $this->addFilter(
                            "age",
                            FiltersInRequestTestRetainedAgeMultipleFilter::class
                        );
                    }
                };
            }
        );

        $this->buildTheWorld();

        // First call to retain the filter on session
        $this->json('get', '/sharp/api/list/person?filter_age=30,32');

        // Second call: filter should be valued
        $this->json('get', '/sharp/api/list/person')
            ->assertStatus(200)
            ->assertJsonFragment(["data" => [
                "items" => [
                    ["id" => 1, "name" => "John", "age" => 30],
                    ["id" => 2, "name" => "Mary", "age" => 32],
                ]
            ]]);

        // Third call: filter should be reset
        $this->json('get', '/sharp/api/list/person?filter_age=')
            ->assertStatus(200)
            ->assertJsonFragment(["data" => [
                "items" => [
                    ["id" => 1, "name" => "John", "age" => 30],
                    ["id" => 2, "name" => "Mary", "age" => 32],
                    ["id" => 3, "name" => "Baby", "age" => 2],
                ]
            ]]);
    }

    /** @test */
    public function retained_filter_works_with_required_filter()
    {
        app()->bind(
            PersonSharpEntityList::class,
            function() {
                return new class() extends PersonSharpEntityList {
                    function getListData(EntityListQueryParams $params)
                    {
                        $items = [
                            ["id" => 1, "name" => "John", "age" => 30],
                            ["id" => 2, "name" => "Mary", "age" => 32],
                            ["id" => 3, "name" => "Baby", "age" => 2],
                        ];

                        $items = collect($items)
                            ->where("age", $params->filterFor("age"))
                            ->values();

                        return $this->transform($items);
                    }
                    function buildListConfig()
                    {
                        $this->addFilter(
                            "age",
                            FiltersInRequestTestRetainedAgeRequiredFilter::class
                        );
                    }
                };
            }
        );

        $this->buildTheWorld();

        // First call to retain the filter on session (default is 2)
        $this->json('get', '/sharp/api/list/person?filter_age=30');

        // Second call: filter should be valued to 30
        $this->json('get', '/sharp/api/list/person')
            ->assertStatus(200)
            ->assertJsonFragment(["data" => [
                "items" => [
                    ["id" => 1, "name" => "John", "age" => 30],
                ]
            ]]);
    }
}

class FiltersInRequestTestRetainedActiveFilter implements ListFilter
{
    public function values()
    {
        return [0, 1];
    }
    public function retainValueInSession()
    {
        return true;
    }
}

class FiltersInRequestTestRetainedAgeMultipleFilter implements ListMultipleFilter
{
    public function values()
    {
        return range(0, 80);
    }

    public function retainValueInSession()
    {
        return true;
    }
}

class FiltersInRequestTestRetainedAgeRequiredFilter implements ListRequiredFilter
{
    public function values()
    {
        return range(0, 80);
    }

    public function defaultValue()
    {
        return 2;
    }

    public function retainValueInSession()
    {
        return true;
    }
}