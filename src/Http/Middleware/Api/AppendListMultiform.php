<?php

namespace Code16\Sharp\Http\Middleware\Api;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppendListMultiform
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
        return $response->status() == 200
            ? $this->addMultiformDataToJsonResponse($response)
            : $response;
    }

    protected function addMultiformDataToJsonResponse(JsonResponse $jsonResponse)
    {
        $subFormKeys = collect($this->getMultiformKeys())
            ->map(function($value) {
                return [
                    "key" => $value,
                    "label" => $this->getMultiformLabelFor($value),
                    "instances" => []
                ];
            })
            ->keyBy("key");

        if(sizeof($subFormKeys)) {
            $data = $jsonResponse->getData();
            $data->forms = $subFormKeys;
            $jsonResponse->setData($data);
        }

        return $jsonResponse;
    }

    /**
     * @return null|string
     */
    protected function determineEntityKey()
    {
        return request()->segment(4);
    }

    /**
     * @return array
     */
    protected function getMultiformKeys(): array
    {
        $entityKey = $this->determineEntityKey();

        $config = config("sharp.entities.{$entityKey}.forms");

        return $config ? array_keys($config) : [];
    }

    /**
     * @param string $formSubKey
     * @return string|null
     */
    protected function getMultiformLabelFor(string $formSubKey)
    {
        $entityKey = $this->determineEntityKey();

        return config("sharp.entities.{$entityKey}.forms.{$formSubKey}.label");
    }
}