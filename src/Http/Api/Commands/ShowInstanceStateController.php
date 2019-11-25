<?php

namespace Code16\Sharp\Http\Api\Commands;

use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;
use Code16\Sharp\Http\Api\ApiController;

class ShowInstanceStateController extends ApiController
{
    use HandleCommandReturn;

    /**
     * @param string $entityKey
     * @param string|null $instanceId
     * @return \Illuminate\Http\JsonResponse
     * @throws SharpAuthorizationException
     * @throws \Code16\Sharp\Exceptions\EntityList\SharpInvalidEntityStateException
     * @throws \Code16\Sharp\Exceptions\SharpInvalidEntityKeyException
     */
    public function update($entityKey, $instanceId = null)
    {
        $showPage = $this->getShowInstance($entityKey);
        $showPage->buildShowConfig();

        if(!$showPage->entityStateHandler()->authorize()
            || !$showPage->entityStateHandler()->authorizeFor($instanceId)) {
            throw new SharpAuthorizationException();
        }

        return $this->returnCommandResult(
            $showPage,
            array_merge(
                $showPage->entityStateHandler()->execute($instanceId, request()->only("value")),
                ["value" => request("value")]
            )
        );
    }
}