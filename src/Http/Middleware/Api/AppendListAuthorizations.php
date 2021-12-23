<?php

namespace Code16\Sharp\Http\Middleware\Api;

use Closure;
use Code16\Sharp\Auth\SharpAuthorizationManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppendListAuthorizations
{
    public function __construct(protected SharpAuthorizationManager $sharpAuthorizationManager) {}

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Add authorization to the JSON returned
        return $response->status() == 200
            ? $this->addAuthorizationsToJsonResponse($response)
            : $response;
    }

    protected function addAuthorizationsToJsonResponse(JsonResponse $jsonResponse)
    {
        $entityKey = $this->determineEntityKey();

        $authorizations["create"] = $this->sharpAuthorizationManager->isAllowed("create", $entityKey);

        // Collect instanceIds from response
        collect($jsonResponse->getData()->data->list->items)
            ->pluck($jsonResponse->getData()->config->instanceIdAttribute)
            ->each(function($instanceId) use (&$authorizations, $entityKey) {
                if($this->sharpAuthorizationManager->isAllowed("view", $entityKey, $instanceId)) {
                    $authorizations["view"][] = $instanceId;
                }
                if($this->sharpAuthorizationManager->isAllowed("update", $entityKey, $instanceId)) {
                    $authorizations["update"][] = $instanceId;
                }
            });

        return tap($jsonResponse, function ($jsonResponse) use ($authorizations) {
            $data = $jsonResponse->getData();
            $data->authorizations = $authorizations;
            $jsonResponse->setData($data);
        });
    }
    
    protected function determineEntityKey(): ?string
    {
        return request()->segment(4);
    }
}