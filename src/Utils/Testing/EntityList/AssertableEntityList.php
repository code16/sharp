<?php

namespace Code16\Sharp\Utils\Testing\EntityList;

use Illuminate\Testing\TestResponse;

class AssertableEntityList
{
    public function __construct(
        protected TestResponse $response,
    ) {}

    public function assertOk(): self
    {
        $this->response->assertOk();

        return $this;
    }
}
