<?php

namespace Code16\Sharp\Http\Middleware\Api;

use Closure;
use Code16\Sharp\Http\Context\CurrentSharpRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * This middleware is responsible for appending the breadcrumb array
 * (containing current navigation path) to the JSON response.
 */
class AppendBreadcrumb
{
    /** @var CurrentSharpRequest */
    protected $currentSharpRequest;

    public function __construct(CurrentSharpRequest $currentSharpRequest)
    {
        $this->currentSharpRequest = $currentSharpRequest;
    }

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        return $response->isOk()
            ? $this->addBreadcrumbToJsonResponse($response)
            : $response;
    }

    protected function addBreadcrumbToJsonResponse(JsonResponse $jsonResponse): JsonResponse
    {
        $url = sharp_base_url_segment();
        $data = $jsonResponse->getData();
        $data->breadcrumb = [
            "items" => $this->currentSharpRequest
                ->breadcrumb()
                ->map(function($item) use(&$url) {
                    $url = sprintf('%s/%s/%s', 
                        $url, 
                        $item->type,
                        isset($item->instance) ? "{$item->key}/{$item->instance}" : $item->key
                    );
                    return [
                        "type" => $this->getFrontTypeNameFor($item->type),
                        "name" => $this->getBreadcrumbLabelFor($item),
                        "entityKey" => $item->key,
                        "url" => url($url)
                    ];
                }),
            'visible' => config("sharp.display_breadcrumb", false),
        ];
        $jsonResponse->setData($data);

        return $jsonResponse;
    }

    private function getFrontTypeNameFor(string $type): string
    {
        return [
            "s-list" => "entityList",
            "s-form" => "form",
            "s-show" => "show",
            "s-dashboard" => "dashboard"
        ][$type] ?? "";
    }

    private function getBreadcrumbLabelFor(object $item)
    {
        switch ($item->type) {
            case "s-list":
                return trans("sharp::breadcrumb.entityList");
            case "s-dashboard":
                return trans("sharp::breadcrumb.dashboard");
            case "s-show":
                return trans("sharp::breadcrumb.show", ["entity" => $this->getEntityLabel($item->key)]);
            case "s-form":
                // A Form is always a leaf
                $previousItem = $this->currentSharpRequest->breadcrumb()[$item->depth-1];
                if(isset($item->instance) || ($previousItem->type === "s-show" && !isset($previousItem->instance))) {
                    if($previousItem->key !== $item->key) {
                        // The form entityKey is different from the previous entityKey
                        // in the breadcrumb: we are in a EEL case.
                        return trans("sharp::breadcrumb.form.edit_entity", ["entity" => $this->getEntityLabel($item->key)]);
                    }
                    return trans("sharp::breadcrumb.form.edit");
                }
                return trans("sharp::breadcrumb.form.create", ["entity" => $this->getEntityLabel($item->key)]);
        }
        
        return $item->key;
    }

    private function getEntityLabel(?string $entityKey): string
    {
        return $entityKey
            ? config("sharp.entities.$entityKey.label", $entityKey)
            : "";
    }
}
