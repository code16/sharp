<?php

namespace Code16\Sharp\Http\Controllers\Api;

use Code16\Sharp\Utils\Entities\ValueObjects\EntityKey;

class ApiFormRefreshController extends ApiController
{
    use HandlesFieldContainer;

    public function update(string $globalFilter, EntityKey $entityKey)
    {
        $fieldContainer = $this->getFieldContainer($entityKey);

        return response()->json([
            'form' => [
                'data' => $fieldContainer->formatHtmlFields(
                    request()->all(),
                    keepOnlyRefreshableFields: true,
                ),
            ],
        ]);
    }
}
