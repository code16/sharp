<?php

namespace Code16\Sharp\Utils\Testing\Show;

use Closure;
use Code16\Sharp\Utils\Testing\DelegatesToResponse;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Testing\TestResponse;
use Inertia\Testing\AssertableInertia;

class AssertableShow
{
    use DelegatesToResponse;

    public function __construct(
        protected TestResponse $response,
    ) {}

    /**
     * @param  Closure(AssertableJson): mixed  $callback
     */
    public function assertShowData(Closure $callback): static
    {
        $this->response->assertInertia(fn (AssertableInertia $page) => $page
            ->has('_rawData', fn (AssertableJson $json) => $callback($json))
            ->etc()
        );

        return $this;
    }
}
