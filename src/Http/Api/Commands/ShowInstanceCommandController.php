<?php

namespace Code16\Sharp\Http\Api\Commands;

use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;
use Code16\Sharp\Http\Api\ApiController;
use Code16\Sharp\Show\SharpSingleShow;

class ShowInstanceCommandController extends ApiController
{
    use HandleCommandReturn;

    /**
     * Display the Command form.
     *
     * @param string      $entityKey
     * @param string      $commandKey
     * @param string|null $instanceId
     *
     * @throws SharpAuthorizationException
     * @throws \Code16\Sharp\Exceptions\SharpInvalidEntityKeyException
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($entityKey, $commandKey, $instanceId = null)
    {
        $showPage = $this->getShowPage($entityKey, $instanceId);
        $commandHandler = $this->getInstanceCommandHandler($showPage, $commandKey, $instanceId);

        return response()->json([
            'data' => $commandHandler->formData($instanceId),
        ]);
    }

    /**
     * Execute the Command.
     *
     * @param string      $entityKey
     * @param string      $commandKey
     * @param string|null $instanceId
     *
     * @throws SharpAuthorizationException
     * @throws \Code16\Sharp\Exceptions\SharpInvalidEntityKeyException
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($entityKey, $commandKey, $instanceId = null)
    {
        $showPage = $this->getShowPage($entityKey, $instanceId);
        $commandHandler = $this->getInstanceCommandHandler($showPage, $commandKey, $instanceId);

        return $this->returnCommandResult(
            $showPage,
            $commandHandler->execute(
                $instanceId,
                $commandHandler->formatRequestData((array) request('data'), $instanceId)
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
