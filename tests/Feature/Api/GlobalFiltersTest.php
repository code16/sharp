<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Utils\Filters\GlobalRequiredFilter;
use Illuminate\Support\Str;

class GlobalFiltersTest extends BaseApiTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    /** @test */
    public function we_can_retrieve_a_global_required_filter_value_from_context()
    {
        $this->buildTheWorld();

        config()->set('sharp.global_filters', [GlobalFiltersTestGlobalRequiredFilter::class]);

        // First call without any value in session
        $this->getJson('/sharp/api/form/person/50');

        $this->assertEquals(
            app(GlobalFiltersTestGlobalRequiredFilter::class)->defaultValue(),
            currentSharpRequest()->globalFilterFor(GlobalFiltersTestGlobalRequiredFilter::class),
        );

        // Second call with a value in session
        $key = (new GlobalFiltersTestGlobalRequiredFilter)->getKey();
        $value = Str::random();
        session()->put("_sharp_retained_global_filter_$key", $value);

        $this->getJson('/sharp/api/form/person/50');

        $this->assertEquals(
            $value,
            currentSharpRequest()->globalFilterFor(GlobalFiltersTestGlobalRequiredFilter::class),
        );
    }

    /** @test */
    public function we_can_set_a_global_filter_value_via_the_endpoint()
    {
        $this->buildTheWorld();

        config()->set('sharp.global_filters', [GlobalFiltersTestGlobalRequiredFilter::class]);
        $key = (new GlobalFiltersTestGlobalRequiredFilter)->getKey();

        $this
            ->postJson("/sharp/api/filters/$key", ['value' => 5])
            ->assertOk();

        $this->getJson('/sharp/api/form/person/50');

        $this->assertEquals(
            5,
            currentSharpRequest()->globalFilterFor(GlobalFiltersTestGlobalRequiredFilter::class),
        );

        $this
            ->postJson("/sharp/api/filters/$key")
            ->assertOk();

        $this->getJson('/sharp/api/form/person/50');

        $this->assertEquals(
            app(GlobalFiltersTestGlobalRequiredFilter::class)->defaultValue(),
            currentSharpRequest()->globalFilterFor(GlobalFiltersTestGlobalRequiredFilter::class),
        );
    }

    /** @test */
    public function we_cant_set_an_invalid_global_filter_value_via_the_endpoint()
    {
        $this->withoutExceptionHandling();  
        $this->buildTheWorld();

        config()->set('sharp.global_filters.test', GlobalFiltersTestGlobalRequiredFilter::class);
        $key = (new GlobalFiltersTestGlobalRequiredFilter)->getKey();

        $this
            ->postJson("/sharp/api/filters/$key", ['value' => 20])
            ->assertOk();

        $this->getJson('/sharp/api/form/person/50');

        $this->assertEquals(
            app(GlobalFiltersTestGlobalRequiredFilter::class)->defaultValue(),
            currentSharpRequest()->globalFilterFor(GlobalFiltersTestGlobalRequiredFilter::class)
        );
    }

    /** @test */
    public function we_can_get_global_filter_values_via_the_endpoint()
    {
        $this->buildTheWorld();

        config()->set('sharp.global_filters', [GlobalFiltersTestGlobalRequiredFilter::class]);

        $this
            ->getJson('/sharp/api/filters')
            ->assertOk()
            ->assertJson([
                'filters' => [
                    [
                        'key' => (new GlobalFiltersTestGlobalRequiredFilter)->getKey(),
                        'multiple' => false,
                        'required' => true,
                        'default' => app(GlobalFiltersTestGlobalRequiredFilter::class)->defaultValue(),
                    ],
                ],
            ]);
    }
}

class GlobalFiltersTestGlobalRequiredFilter extends GlobalRequiredFilter
{
    public function values(): array
    {
        return range(0, 10);
    }

    public function defaultValue(): mixed
    {
        return 8;
    }
}
