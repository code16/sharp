<?php

namespace Code16\Sharp\Http\Api;

class EntityListController extends ApiController
{
    public function show(string $entityKey)
    {
        sharp_check_ability('entity', $entityKey);

        $list = $this->getListInstance($entityKey);
        $list->buildListConfig();

        return response()->json([
            'containers' => $list->dataContainers(),
            'layout' => $list->listLayout(),
            'data' => $list->data(),
            'config' => $list->listConfig(config()->has("sharp.entities.{$entityKey}.show")),
        ]);
    }

    /**
     * Call for reorder instances.
     */
    public function update(string $entityKey)
    {
        sharp_check_ability('update', $entityKey);

        $list = $this->getListInstance($entityKey);
        $list->buildListConfig();

        $list->reorderHandler()->reorder(
            request('instances'),
        );

        return response()->json([
            'ok' => true,
        ]);
    }
}
