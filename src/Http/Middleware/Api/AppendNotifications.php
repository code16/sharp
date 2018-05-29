<?php

namespace Code16\Sharp\Http\Middleware\Api;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppendNotifications
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        return $response->status() == 200
            ? $this->addAlertToResponse($response)
            : $response;
    }

    protected function addAlertToResponse(JsonResponse $jsonResponse)
    {
        if(! $alert = session("sharp_notification")) {
            return $jsonResponse;
        }

        session()->forget("sharp_notification");

        return tap($jsonResponse, function($jsonResponse) use($alert) {
            $data = $jsonResponse->getData();
            $data->alert = $alert;
            $jsonResponse->setData($data);
        });
    }
}