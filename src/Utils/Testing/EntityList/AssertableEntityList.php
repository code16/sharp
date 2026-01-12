<?php

namespace Code16\Sharp\Utils\Testing\EntityList;

use Code16\Sharp\Utils\Testing\DelegatesToResponse;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert as PHPUnit;

class AssertableEntityList
{
    use DelegatesToResponse;

    public function __construct(
        protected TestResponse $response,
    ) {}

    public function assertListCount(int $count): self
    {
        PHPUnit::assertCount($count, $this->listData());

        return $this;
    }

    public function assertListContains(array $attributes): self
    {
        PHPUnit::assertTrue(
            collect($this->listData())->contains(fn ($item) => collect($attributes)->every(fn ($value, $key) => isset($item[$key]) && $item[$key] === $value)
            ),
            sprintf(
                'Failed asserting that data contains an item with attributes: %s',
                json_encode($attributes)
            )
        );

        return $this;
    }

    protected function listData(): array
    {
        return $this->response->inertiaProps('entityList.data');
    }
}
