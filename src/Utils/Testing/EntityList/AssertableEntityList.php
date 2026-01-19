<?php

namespace Code16\Sharp\Utils\Testing\EntityList;

use Closure;
use Code16\Sharp\Utils\Testing\DelegatesToResponse;
use Code16\Sharp\Utils\Testing\Show\PendingShow;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Testing\TestResponse;
use Inertia\Testing\AssertableInertia;

class AssertableEntityList
{
    use DelegatesToResponse;

    public function __construct(
        protected TestResponse $response,
        protected PendingEntityList $pendingEntityList,
    ) {}

    /**
     * @param  Closure(AssertableJson): mixed  $callback
     */
    public function assertListData(Closure $closure): static
    {
        if ($this->pendingEntityList->parent instanceof PendingShow) {
            $this->response->assertJson(fn (AssertableJson $json) => $json
                ->has('data', $closure)
                ->etc()
            );
        } else {
            $this->response->assertInertia(fn (AssertableInertia $page) => $page
                ->has('entityList.data', $closure)
                ->etc()
            );
        }

        return $this;
    }
}
