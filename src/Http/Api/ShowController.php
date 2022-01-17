<?php

namespace Code16\Sharp\Http\Api;

use Code16\Sharp\Show\SharpShow;
use Code16\Sharp\Show\SharpSingleShow;

class ShowController extends ApiController
{
    public function show(string $entityKey, string $instanceId = null)
    {
        sharp_check_ability('view', $entityKey, $instanceId);

        $show = $this->getShowInstance($entityKey);

        abort_if(
            (!$instanceId && !$show instanceof SharpSingleShow)
            || ($instanceId && $show instanceof SharpSingleShow),
            404
        );

        $show->buildShowConfig();

        return response()->json(
            [
                'config' => $show->showConfig($instanceId),
                'fields' => $show->fields(),
                'layout' => $show->showLayout(),
                'data'   => $show->instance($instanceId),
            ]
            + $this->dataLocalizations($show)
        );
    }

    protected function dataLocalizations(SharpShow $show): array
    {
        return [];
    }
}
