<?php

namespace Code16\Sharp\Http\Middleware\Api;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppendMultiformInEntityList
{
    /**
     * @param  Request  $request
     * @param  Closure  $next
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Add Multiform data to the JSON returned
        return $response->status() == 200
            ? $this->addMultiformDataToJsonResponse($response)
            : $response;
    }

    protected function addMultiformDataToJsonResponse(JsonResponse $jsonResponse)
    {
        if (! $multiformAttribute = $jsonResponse->getData()->config->multiformAttribute) {
            return $jsonResponse;
        }

        $instanceIdAttribute = $jsonResponse->getData()->config->instanceIdAttribute;

        $subFormKeys = collect($this->getMultiformKeys())
            ->map(function ($value) use ($instanceIdAttribute, $multiformAttribute, $jsonResponse) {
                $instanceIds = collect($jsonResponse->getData()->data->items)
                    ->where($multiformAttribute, $value)
                    ->pluck($instanceIdAttribute);

                return [
                    'key' => $value,
                    'label' => $this->getMultiformLabelFor($value),
                    'instances' => $instanceIds,
                ] + $this->getIconConfigFor($value);
            })
            ->keyBy('key');

        if (sizeof($subFormKeys)) {
            $data = $jsonResponse->getData();
            $data->forms = $subFormKeys;
            $jsonResponse->setData($data);
        }

        return $jsonResponse;
    }

    protected function determineEntityKey(): ?string
    {
        return request()->segment(4);
    }

    protected function getMultiformKeys(): array
    {
        $entityKey = $this->determineEntityKey();

        $config = config("sharp.entities.{$entityKey}.forms");

        return $config ? array_keys($config) : [];
    }

    protected function getMultiformLabelFor(string $formSubKey): ?string
    {
        $entityKey = $this->determineEntityKey();

        return config("sharp.entities.{$entityKey}.forms.{$formSubKey}.label");
    }

    protected function getIconConfigFor(string $formSubKey): array
    {
        $entityKey = $this->determineEntityKey();

        $icon = config("sharp.entities.{$entityKey}.forms.{$formSubKey}.icon");

        return $icon ? compact('icon') : [];
    }
}
