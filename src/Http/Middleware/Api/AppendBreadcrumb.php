<?php

namespace Code16\Sharp\Http\Middleware\Api;

use Closure;
use Code16\Sharp\Http\Context\CurrentSharpRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

/**
 * This middleware is responsible for appending the breadcrumb array
 * (containing current navigation path) to the JSON response.
 */
class AppendBreadcrumb
{
    protected CurrentSharpRequest $currentSharpRequest;
    protected ?object $data = null;

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
        $breadcrumb = $this->currentSharpRequest->breadcrumb();
        $displayBreadcrumb = config("sharp.display_breadcrumb", false);
        $this->data = $jsonResponse->getData();
        
        $this->data->breadcrumb = [
            "items" => $breadcrumb
                ->map(function($item, $index) use(&$url, $displayBreadcrumb, $breadcrumb) {
                    $url = sprintf('%s/%s/%s',
                        $url,
                        $item->type,
                        isset($item->instance) ? "{$item->key}/{$item->instance}" : $item->key
                    );
                    
                    return [
                        "type" => $this->getFrontTypeNameFor($item->type),
                        "name" => $displayBreadcrumb 
                            ? $this->getBreadcrumbLabelFor($item, $index === sizeof($breadcrumb)-1)
                            : "",
                        "entityKey" => $item->key,
                        "url" => url($url)
                    ];
                }),
            'visible' => $displayBreadcrumb,
        ];
        $jsonResponse->setData($this->data);

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

    private function getBreadcrumbLabelFor(object $item, bool $isLeaf)
    {
        switch ($item->type) {
            case "s-list":
                return currentSharpRequest()->getEntityMenuLabel($item->key) ?: trans("sharp::breadcrumb.entityList");
            case "s-dashboard":
                return currentSharpRequest()->getEntityMenuLabel($item->key) ?: trans("sharp::breadcrumb.dashboard");
            case "s-show":
                return trans("sharp::breadcrumb.show", [
                    "entity" => $this->getEntityLabelForInstance($item, $isLeaf)
                ]);
            case "s-form":
                // A Form is always a leaf
                $previousItem = $this->currentSharpRequest->breadcrumb()[$item->depth-1];

                if($previousItem->type === "s-show" /*&& isset($previousItem->instance)*/ && !$this->isSameEntityKeys($previousItem->key, $item->key, true)) {
                    // The form entityKey is different from the previous entityKey in the breadcrumb: we are in a EEL case.
                    return isset($item->instance)
                        ? trans("sharp::breadcrumb.form.edit_entity", [
                            "entity" => $this->getEntityLabelForInstance($item, true)
                        ])
                        : trans("sharp::breadcrumb.form.create_entity", [
                            "entity" => $this->getEntityLabelForInstance($item, true)
                        ]);
                }
                
                return isset($item->instance) || ($previousItem->type === "s-show" && !isset($previousItem->instance))
                    ? trans("sharp::breadcrumb.form.edit")
                    : trans("sharp::breadcrumb.form.create");
        }
        
        return $item->key;
    }

    /**
     * Only for Shows and Forms.
     */
    private function getEntityLabelForInstance(object $item, bool $isLeaf): string
    {
        $cacheKey = "sharp.breadcrumb.{$item->key}.{$item->type}.{$item->instance}";
        
        if($isLeaf && $breadcrumbAttribute =  $this->data->config->breadcrumbAttribute ?? null) {
            if($value = Arr::get(json_decode(json_encode($this->data->data), true), $breadcrumbAttribute)) {
                Cache::put($cacheKey, $value, now()->addMinutes(30));
                return $value;
            }
        }
        
        if(!$isLeaf) {
            // The breadcrumb custom label may have been cached on the way up
            if($value = Cache::get("sharp.breadcrumb.{$item->key}.{$item->type}.{$item->instance}")) {
                return $value;
            }
        }
        
        if(strpos($item->key, ':') !== false) {
            list($itemKey, $itemSubKey) = explode(":", $item->key);
            if($value = config("sharp.entities.{$itemKey}.forms.{$itemSubKey}.label")) {
                return $value;
            }

            return config("sharp.entities.{$itemKey}.label", $itemKey);
        }
        
        return config("sharp.entities.{$item->key}.label", $item->key);
    }

    private function isSameEntityKeys(string $key1, string $key2, bool $compareBaseEntities): bool
    {
        if($compareBaseEntities) {
            $key1 = explode(":", $key1)[0];
            $key2 = explode(":", $key2)[0];
        }
        
        return $key1 === $key2;
    }
}
