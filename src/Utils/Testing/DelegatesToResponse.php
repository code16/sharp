<?php

namespace Code16\Sharp\Utils\Testing;

use Illuminate\Testing\TestResponse;

/**
 * @method
 *
 * @mixin TestResponse
 */
trait DelegatesToResponse
{
    protected TestResponse $response;

    public function __call(string $name, array $arguments)
    {
        $this->response->{$name}(...$arguments);

        return $this;
    }

    public function __get(string $name)
    {
        return $this->response->{$name};
    }
}
