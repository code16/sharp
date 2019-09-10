<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Http\SharpContext;
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

        config()->set("sharp.global_filters.req_test", GlobalFiltersTestGlobalRequiredFilter::class);

        $context = app(SharpContext::class);

        // First call without any value in session
        $this->json('get', '/sharp/api/form/person/50');

        $this->assertEquals("default", $context->globalFilterFor("req_test"));

        // Second call with a value in session
        $value = Str::random();
        session()->put("_sharp_retained_global_filter_req_test", $value);

        $this->json('get', '/sharp/api/form/person/50');

        $this->assertEquals($value, $context->globalFilterFor("req_test"));
    }

    /** @test */
    function we_can_set_a_global_filter_value_via_the_endpoint()
    {
        $this->buildTheWorld();
        $context = app(SharpContext::class);

        config()->set("sharp.global_filters.test", GlobalFiltersTestGlobalRequiredFilter::class);

        $this
            ->postJson('/sharp/api/filters/test', ["value" => 5])
            ->assertStatus(200);

        $this->json('get', '/sharp/api/form/person/50');

        $this->assertEquals(5, $context->globalFilterFor("test"));

        $this
            ->postJson('/sharp/api/filters/test')
            ->assertStatus(200);

        $this->json('get', '/sharp/api/form/person/50');

        $this->assertEquals("default", $context->globalFilterFor("test"));
    }

    /** @test */
    function we_cant_set_an_invalid_global_filter_value_via_the_endpoint()
    {
        $this->buildTheWorld();
        $context = app(SharpContext::class);

        config()->set("sharp.global_filters.test", GlobalFiltersTestGlobalRequiredFilter::class);

        $this
            ->postJson('/sharp/api/filters/test', ["value" => 20])
            ->assertStatus(200);

        $this->json('get', '/sharp/api/form/person/50');

        $this->assertEquals("default", $context->globalFilterFor("test"));
    }

    /** @test */
    function we_can_get_global_filter_values_via_the_endpoint()
    {
        $this->buildTheWorld();

        config()->set("sharp.global_filters.test", GlobalFiltersTestGlobalRequiredFilter::class);

        $this
            ->getJson('/sharp/api/filters')
            ->assertStatus(200)
            ->assertJson([
                "filters" => [
                    [
                        "key" => "test",
                        "multiple" => false,
                        "required" => true,
                        "default" => "default",
                    ],
                ]
            ]);
    }
}

class GlobalFiltersTestGlobalRequiredFilter implements GlobalRequiredFilter
{
    public function values()
    {
        return range(0, 10);
    }

    public function defaultValue()
    {
        return "default";
    }
}