<?php

namespace Code16\Sharp\Tests\Unit\Utils;

use Code16\Sharp\Http\Context\CurrentSharpRequest;
use Illuminate\Support\Collection;

trait FakesCurrentSharpRequest
{
    protected function fakeCurrentSharpRequestWithUrl(string $url): void
    {
        app()->bind(
            CurrentSharpRequest::class,
            function () use ($url) {
                return new class($url) extends CurrentSharpRequest
                {
                    public string $url;

                    public function __construct(string $url)
                    {
                        $this->url = $url;
                    }

                    protected function getSegmentsFromRequest(): Collection
                    {
                        return collect(explode('/', parse_url(url($this->url))['path']))
                            ->filter(fn (string $segment) => strlen(trim($segment))
                                && $segment !== sharp()->config()->get('custom_url_segment')
                            )
                            ->values();
                    }
                };
            });
    }
}
