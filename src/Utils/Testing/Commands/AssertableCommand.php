<?php

namespace Code16\Sharp\Utils\Testing\Commands;

use Code16\Sharp\Utils\Testing\DelegatesToResponse;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert as PHPUnit;

class AssertableCommand
{
    use DelegatesToResponse;

    public function __construct(
        protected TestResponse $response,
    ) {}

    public function assertReturnsHtml(): static
    {
        $this->response->assertJson(fn (AssertableJson $json) => $json->where('action', 'html')
        );

        return $this;
    }

    public function assertReturnsInfo(string $message = ''): static
    {
        $this->response->assertJson(fn (AssertableJson $json) => $json->where('action', 'info')
            ->when($message)->where('message', $message)
            ->etc()
        );

        return $this;
    }

    public function assertReturnsLink(string $url = ''): static
    {
        $this->response->assertJson(fn (AssertableJson $json) => $json->where('action', 'link')
            ->when($url)->where('url', $url)
        );

        return $this;
    }

    public function assertReturnsReload(): static
    {
        $this->response->assertJson(fn (AssertableJson $json) => $json->where('action', 'reload')
        );

        return $this;
    }

    public function assertReturnsRefresh(): static
    {
        $this->response->assertJson(fn (AssertableJson $json) => $json->where('action', 'refresh')
        );

        return $this;
    }

    public function assertReturnsStep(string $step): static
    {
        $this->response->assertJson(fn (AssertableJson $json) => $json->where('action', 'step')
        );

        PHPUnit::assertEquals($step, Str::before($this->response->json('step'), ':'));

        return $this;
    }
}
