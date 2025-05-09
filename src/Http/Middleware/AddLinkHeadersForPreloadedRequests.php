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
        if ($request->hasHeader('X-No-Preload') || $this->isSafari()) {
            return $next($request);
        }

        return tap($next($request), function (Response $response) {
            if ($this->preloadedRequests !== []) {
                if ($link = $response->headers->get('Link', '')) {
                    $link .= ', ';
                }

                $link .= collect($this->preloadedRequests)
                    ->map(fn ($url) => sprintf(
                        '<%s>; rel="preload"; as="fetch"; type="application/json"; %s',
                        $url,
                        $this->isSafari() ? '' : 'crossorigin="anonymous"'
                    ))
                    ->join(', ');

                $response->headers->set('Link', $link);
            }
        });
    }

    private function isSafari(): bool
    {
        return str_contains(request()->userAgent(), 'Safari')
            && ! str_contains(request()->userAgent(), 'Chrome');
    }
}
