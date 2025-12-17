<?php

namespace Code16\Sharp\Http\Controllers\Api;

use Code16\Sharp\Filters\AutocompleteRemoteFilter;
use Code16\Sharp\Utils\Entities\SharpDashboardEntity;
use Code16\Sharp\Utils\Entities\ValueObjects\EntityKey;

class ApiFilterAutocompleteController extends ApiController
{
    public function index(string $filterKey, EntityKey $entityKey, string $filterHandlerKey): array
    {
        $entity = $this->entityManager->entityFor($entityKey);

        if ($entity instanceof SharpDashboardEntity) {
            $filter = $entity->getViewOrFail()->filterContainer()->findFilterHandler($filterHandlerKey);
        } else {
            $filter = $entity->getListOrFail()->filterContainer()->findFilterHandler($filterHandlerKey);
        }

        if (! $filter instanceof AutocompleteRemoteFilter) {
            return [
                'data' => [],
            ];
        }

        $query = request()->input('query');
        $values = $filter->values($query ?: '');

        return [
            'data' => $filter->format($values),
        ];
    }
}
