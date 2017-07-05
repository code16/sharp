<?php

namespace Code16\Sharp\Http\Api;

class EntitiesListController extends ApiController
{

    /**
     * @param string $entityKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($entityKey)
    {
        $list = $this->getListInstance($entityKey);

        return response()->json([
            "containers" => $list->dataContainers(),
            "layout" => $list->listLayout(),
            "data" => $list->data(),
            "config" => $list->listConfig()
        ]);
    }
}