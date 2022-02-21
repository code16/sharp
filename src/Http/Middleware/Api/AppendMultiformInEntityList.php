<?php

namespace Code16\Sharp\Http\Middleware\Api;

use Closure;
use Code16\Sharp\Exceptions\SharpInvalidConfigException;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppendMultiformInEntityList
{
    public function __construct(protected SharpEntityManager $sharpEntityManager)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Add Multiform data to the JSON returned
        return $response->isOk()
            ? $this->addMultiformDataToJsonResponse($response)
            : $response;
    }

    protected function addMultiformDataToJsonResponse(JsonResponse $jsonResponse): JsonResponse
    {
        $entityKey = $this->determineEntityKey();
        $jsonData = $jsonResponse->getData();

        if (! $jsonData->config->multiformAttribute) {
            return $jsonResponse;
        }

        if (! $forms = $this->sharpEntityManager->entityFor($entityKey)->getMultiforms()) {
            throw new SharpInvalidConfigException("The list for the entity [$entityKey] defines a multiform attribute [{$jsonData->config->multiformAttribute}] but the entity is not configured as multiform.");
        }

        $subFormKeys = collect($forms)
            ->map(function ($value, $key) use ($jsonData) {
                $instanceIds = collect($jsonData->data->list->items)
                    ->where($jsonData->config->multiformAttribute, $key)
                    ->pluck($jsonData->config->instanceIdAttribute);

                return [
                    'key' => $key,
                    'label' => is_array($value) && sizeof($value) > 1 ? $value[1] : $key,
                    'instances' => $instanceIds,
                ];
            })
            ->keyBy('key');

        if (sizeof($subFormKeys)) {
            $jsonData->forms = $subFormKeys;
            $jsonResponse->setData($jsonData);
        }

        return $jsonResponse;
    }

    protected function determineEntityKey(): ?string
    {
        return request()->segment(4);
    }
}
