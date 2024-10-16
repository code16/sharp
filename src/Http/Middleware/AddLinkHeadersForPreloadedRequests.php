<?php

namespace Code16\Sharp\Http\Middleware;



use Symfony\Component\HttpFoundation\Response;

class AddLinkHeadersForPreloadedRequests
{
    protected array $preloadedRequests = [];
    
    public function preload(string $endpointUrl): void
    {
        $this->preloadedRequests[] = $endpointUrl;
    }
    
    public function handle($request, $next)
    {
        return tap($next($request), function (Response $response) {
            if ($this->preloadedRequests !== []) {
                if($link = $response->headers->get('Link', '')) {
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
