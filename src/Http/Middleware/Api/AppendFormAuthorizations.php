<?php

namespace Code16\Sharp\Http\Middleware\Api;

use Closure;
use Code16\Sharp\Auth\SharpAuthorizationManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * This middleware appends authorizations for GET form request to the response.
 */
class AppendFormAuthorizations
{
    public function __construct(protected SharpAuthorizationManager $sharpAuthorizationManager)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        return $response->status() == 200
            ? $this->addAuthorizationsToJsonResponse($response)
            : $response;
    }

    protected function addAuthorizationsToJsonResponse(JsonResponse $jsonResponse): JsonResponse
    {
        [$entityKey, $instanceId] = $this->determineEntityKeyAndInstanceId();

        $data = $jsonResponse->getData();
        $data->authorizations = [
            'create' => $this->sharpAuthorizationManager->isAllowed('create', $entityKey),
            'view' => $this->sharpAuthorizationManager->isAllowed('view', $entityKey, $instanceId),
            'update' => $this->sharpAuthorizationManager->isAllowed('update', $entityKey, $instanceId),
            'delete' => $this->sharpAuthorizationManager->isAllowed('delete', $entityKey, $instanceId),
        ];
        $jsonResponse->setData($data);

        return $jsonResponse;
    }

    protected function determineEntityKeyAndInstanceId(): array
    {
        $entityKey = request()->segment(4);
        $instanceId = request()->segment(5) ?? null;

        return [sharp_normalize_entity_key($entityKey)[0], $instanceId];
    }
}
