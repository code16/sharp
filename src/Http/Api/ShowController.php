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
            (! $instanceId && ! $show instanceof SharpSingleShow)
            || ($instanceId && $show instanceof SharpSingleShow),
            404,
        );

        $show->buildShowConfig();

        return response()->json(
            array_merge(
                [
                    'config' => $show->showConfig($instanceId),
                    'fields' => $show->fields(),
                    'layout' => $show->showLayout(),
                    'data' => $show->instance($instanceId),
                ],
                $this->dataLocalizations($show),
            ),
        );
    }

    public function delete(string $entityKey, string $instanceId)
    {
        sharp_check_ability('delete', $entityKey, $instanceId);

        $show = $this->getShowInstance($entityKey);

        $show->delete($instanceId);

        return response()->json([
            'ok' => true,
        ]);
    }

    protected function dataLocalizations(SharpShow $show): array
    {
        return $show->hasDataLocalizations()
            ? ['locales' => $show->getDataLocalizations()]
            : [];
    }
}
