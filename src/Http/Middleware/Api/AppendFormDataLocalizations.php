<?php

namespace Code16\Sharp\Http\Middleware\Api;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * This middleware appends data localization for GET form request to the response.
 */
class AppendFormDataLocalizations
{

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Add authorization to the JSON returned
        return $response->status() == 200
            ? $this->addDataLocalizationsToJsonResponse($response)
            : $response;
    }

    protected function addDataLocalizationsToJsonResponse(JsonResponse $jsonResponse)
    {
        $locales = config("sharp.locales");

        if(!$locales) {
            return $jsonResponse;
        }

        $data = $jsonResponse->getData();
        $data->locales = $locales;
        $jsonResponse->setData($data);

        return $jsonResponse;
    }
}