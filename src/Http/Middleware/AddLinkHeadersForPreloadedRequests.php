<?php

namespace Code16\Sharp\Http\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddLinkHeadersForPreloadedRequests
{
    protected array $preloadedRequests = [];

    public function preload(string $endpointUrl): void
    {
        $this->preloadedRequests[] = $endpointUrl;
    }

    public function handle(Request $request, $next)
    {
        // Disable preloading fetch in Safari/Firefox as it is not taken into account
        if (! str_contains($request->userAgent(), 'Chrome')) {
            return $next($request);
        }

        return tap($next($request), function (Response $response) {
            if ($this->preloadedRequests !== []) {
                if ($link = $response->headers->get('Link', '')) {
                    $link .= ', ';
                }

                $link .= collect($this->preloadedRequests)
                    ->map(fn ($url) => sprintf('<%s>; rel="preload"; as="fetch"; type="application/json"; crossorigin="anonymous"', $url))
                    ->join(', ');

                $response->headers->set('Link', $link);
            }
        });
    }
}
