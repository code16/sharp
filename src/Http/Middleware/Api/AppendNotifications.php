<?php

namespace Code16\Sharp\Http\Middleware\Api;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppendNotifications
{
    /**
     * @param  Request  $request
     * @param  Closure  $next
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        return $response->status() == 200
            ? $this->addNotificationsToResponse($response)
            : $response;
    }

    protected function addNotificationsToResponse(JsonResponse $jsonResponse)
    {
        if (! $notifications = session('sharp_notifications')) {
            return $jsonResponse;
        }

        session()->forget('sharp_notifications');

        return tap($jsonResponse, function ($jsonResponse) use ($notifications) {
            $data = $jsonResponse->getData();
            $data->notifications = array_values($notifications);
            $jsonResponse->setData($data);
        });
    }
}
