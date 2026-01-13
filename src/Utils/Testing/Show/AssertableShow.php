<?php

namespace Code16\Sharp\Utils\Testing\Show;

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

    public function showData(): array
    {
        return $this->response->inertiaProps('_rawData');
    }

    public function assertShowData(array $expectedData): self
    {
        $this->response->assertInertia(fn (AssertableInertia $page) => $page
            ->has('_rawData', fn (AssertableJson $json) => $json->whereAll($expectedData)->etc())
        );

        return $this;
    }
}
