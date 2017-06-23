<?php

namespace Code16\Sharp\Http\Api;

use Code16\Sharp\EntitiesList\EntitiesListQueryParams;
use Code16\Sharp\EntitiesList\SharpEntitiesList;
use Illuminate\Routing\Controller;

class EntitiesListController extends Controller
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
            "data" => $list->data(new EntitiesListQueryParams())
        ]);
    }

    /**
     * @param string $entityKey
     * @return SharpEntitiesList
     */
    protected function getListInstance(string $entityKey): SharpEntitiesList
    {
        return app(config("sharp.entities.{$entityKey}.list"));
    }
}