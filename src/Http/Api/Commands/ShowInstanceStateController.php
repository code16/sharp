<?php

namespace Code16\Sharp\Http\Api\Commands;

use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;
use Code16\Sharp\Http\Api\ApiController;
use Code16\Sharp\Show\SharpSingleShow;

class ShowInstanceStateController extends ApiController
{
    use HandleCommandReturn;

    /**
     * @param string      $entityKey
     * @param string|null $instanceId
     *
     * @throws SharpAuthorizationException
     * @throws \Code16\Sharp\Exceptions\EntityList\SharpInvalidEntityStateException
     * @throws \Code16\Sharp\Exceptions\SharpInvalidEntityKeyException
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($entityKey, $instanceId = null)
    {
        $showPage = $this->getShowPage($entityKey, $instanceId);
        $stateHandler = $showPage->entityStateHandler();

        if (!$stateHandler->authorize()
            || !$stateHandler->authorizeFor($instanceId)) {
            throw new SharpAuthorizationException();
        }

        return $this->returnCommandResult(
            $showPage,
            array_merge(
                $stateHandler->execute($instanceId, request()->only('value')),
                ['value' => request('value')]
            )
        );
    }

    /**
     * @param string      $entityKey
     * @param string|null $instanceId
     *
     * @throws \Code16\Sharp\Exceptions\SharpInvalidEntityKeyException
     *
     * @return \Code16\Sharp\Show\SharpShow
     */
    private function getShowPage(string $entityKey, $instanceId = null)
    {
        $showPage = $this->getShowInstance($entityKey);

        abort_if(
            (!$instanceId && !$showPage instanceof SharpSingleShow)
            || ($instanceId && $showPage instanceof SharpSingleShow),
            404
        );

        $showPage->buildShowConfig();

        return $showPage;
    }
}
