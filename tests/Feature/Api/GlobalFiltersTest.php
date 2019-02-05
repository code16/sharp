<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Http\SharpContext;
use Code16\Sharp\Utils\Filters\GlobalFilter;
use Code16\Sharp\Utils\Filters\GlobalMultipleFilter;
use Code16\Sharp\Utils\Filters\GlobalRequiredFilter;

class GlobalFiltersTest extends BaseApiTest
{
    protected function setUp()
    {
        parent::setUp();

        $this->login();
    }

    /** @test */
    public function we_can_retrieve_a_global_filter_value_from_context()
    {
        $this->buildTheWorld();

        config()->set("sharp.global_filters.test", GlobalFiltersTestGlobalFilter::class);

        $context = app(SharpContext::class);

        // First call without any value in session
        $this->json('get', '/sharp/api/form/person/50');

        $this->assertNull($context->globalFilterFor("test"));

        // Second call with a value in session
        $value = str_random();
        session()->put("_sharp_retained_global_filter_test", $value);

        $this->json('get', '/sharp/api/form/person/50');

        $this->assertEquals($value, $context->globalFilterFor("test"));
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
        $value = str_random();
        session()->put("_sharp_retained_global_filter_req_test", $value);

        $this->json('get', '/sharp/api/form/person/50');

        $this->assertEquals($value, $context->globalFilterFor("req_test"));
    }

    /** @test */
    public function we_can_retrieve_a_global_multiple_filter_value_from_context()
    {
        $this->buildTheWorld();

        config()->set("sharp.global_filters.mul_test", GlobalFiltersTestGlobalMultipleFilter::class);

        $context = app(SharpContext::class);

        // First call without any value in session
        $this->json('get', '/sharp/api/form/person/50');

        $this->assertNull($context->globalFilterFor("mul_test"));

        // Second call with a value in session
        $value = [str_random(), str_random()];
        session()->put("_sharp_retained_global_filter_mul_test", implode(",", $value));

        $this->json('get', '/sharp/api/form/person/50');

        $this->assertEquals($value, $context->globalFilterFor("mul_test"));
    }
}

class GlobalFiltersTestGlobalFilter implements GlobalFilter
{
    public function values()
    {
        return range(0, 10);
    }
}

class GlobalFiltersTestGlobalRequiredFilter
    extends GlobalFiltersTestGlobalFilter implements GlobalRequiredFilter
{
    public function defaultValue()
    {
        return "default";
    }
}

class GlobalFiltersTestGlobalMultipleFilter
    extends GlobalFiltersTestGlobalFilter implements GlobalMultipleFilter
{
}