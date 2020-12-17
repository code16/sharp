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
    /**
     * @param Request $request
     * @param Closure $next
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Add Multiform data to the JSON returned

        return $response->isOk()
            ? $this->addBreadcrumbToJsonResponse($response)
            : $response;
    }

    /**
     * @param JsonResponse $jsonResponse
     * @return JsonResponse
     */
    protected function addBreadcrumbToJsonResponse(JsonResponse $jsonResponse)
    {
        $data = $jsonResponse->getData();
        $data->breadcrumb = [
            'items' => [],
            'visible' => config("sharp.display_breadcrumb", false),
        ];
        $jsonResponse->setData($data);

        return $jsonResponse;
    }
}
