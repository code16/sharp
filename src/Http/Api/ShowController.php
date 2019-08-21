<?php

namespace Code16\Sharp\Http\Api;

use Code16\Sharp\Exceptions\SharpException;
use Code16\Sharp\Show\SharpShow;
use Code16\Sharp\Show\SharpSingleShow;

class ShowController extends ApiController
{

    /**
     * @param string $entityKey
     * @param string $instanceId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Code16\Sharp\Exceptions\Auth\SharpAuthorizationException
     * @throws \Code16\Sharp\Exceptions\SharpInvalidEntityKeyException
     * @throws SharpException
     */
    public function show($entityKey, $instanceId = null)
    {
        sharp_check_ability("view", $entityKey, $instanceId);

        $show = $this->getShowInstance($entityKey);
        abort_if(
            (!$instanceId && !$show instanceof SharpSingleShow) || ($instanceId && $show instanceof SharpSingleShow),
            404
        );

        $show->buildShowConfig();

        return response()->json([
            "config" => $show->showConfig(config()->has("sharp.entities.{$entityKey}.list")),
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