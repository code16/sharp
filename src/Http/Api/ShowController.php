<?php

namespace Code16\Sharp\Http\Api;

use Code16\Sharp\Show\SharpShow;

class ShowController extends ApiController
{

    /**
     * @param string $entityKey
     * @param string $instanceId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Code16\Sharp\Exceptions\Auth\SharpAuthorizationException
     * @throws \Code16\Sharp\Exceptions\SharpInvalidEntityKeyException
     */
    public function show($entityKey, $instanceId)
    {
        sharp_check_ability("view", $entityKey, $instanceId);

        $show = $this->getShowInstance($entityKey);
        $show->buildShowConfig();

        return response()->json([
            "config" => $show->showConfig(),
            "fields" => $show->fields(),
            "layout" => $show->showLayout(),
            "data" => $show->instance($instanceId)
        ] + $this->dataLocalizations($show));
    }

    /**
     * @param SharpShow $show
     * @return array
     */
    protected function dataLocalizations(SharpShow $show)
    {
        return [];

        return $show->hasDataLocalizations()
            ? ["locales" => $show->getDataLocalizations()]
            : [];
    }
}