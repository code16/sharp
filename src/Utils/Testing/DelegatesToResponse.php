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
        $result = $this->response->{$name}(...$arguments);

        // Keep chaining on fluent assertions, but give back actual values (json(), streamedContent()...)
        return $result === $this->response ? $this : $result;
    }

    public function __get(string $name)
    {
        return $this->response->{$name};
    }
}
