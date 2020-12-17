<?php

namespace Code16\Sharp\Http\Middleware\Api;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * This middleware is responsible for appending the breadcrumb array
 * (containing current navigation path) to the JSON response.
 */
class AppendBreadcrumb
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        return $response->isOk()
            ? $this->addBreadcrumbToJsonResponse($request->header("referer"), $response)
            : $response;
    }

    protected function addBreadcrumbToJsonResponse(string $referer, JsonResponse $jsonResponse): JsonResponse
    {
        $segments = collect(explode("/", parse_url($referer)["path"]))
            ->filter(function(string $segment) {
                return strlen(trim($segment)) && $segment !== "sharp";
            })
            ->values();
        
        $groups[] = [
            $segments[0], 
            $segments[1]
        ];

        $segments = $segments->slice(2)->values();
        
        while($segments->count()) {
            $newGroup = array_merge(
                [$segments->shift()], // First segment is s-form or s-show
                $segments->takeWhile(function(string $segment) {
                    return !in_array($segment, ["s-show", "s-form"]); 
                })
                ->toArray()
            );

            $segments = $segments->slice(count($newGroup)-1)->values();

            $groups[] = $newGroup;
        }

        $url = sharp_base_url_segment();
        $data = $jsonResponse->getData();
        $data->breadcrumb = [
            'items' => collect($groups)
                ->map(function(array $group) use(&$url) {
                    $url = sprintf('%s/%s/%s', 
                        $url, 
                        $group[0],
                        count($group) === 2 ? $group[1] : "{$group[1]}/{$group[2]}"
                    );
                    return [
                        "type" => $this->getTypeFrontNameFor($group[0]),
                        "url" => url($url)
                    ];
                }),
            'visible' => config("sharp.display_breadcrumb", false),
        ];
        $jsonResponse->setData($data);

        return $jsonResponse;
    }

    private function getTypeFrontNameFor(string $type): string
    {
        return [
            "s-list" => "entityList",
            "s-form" => "form",
            "s-show" => "show",
            "s-dashboard" => "dashboard"
        ][$type] ?? "";
    }
}
