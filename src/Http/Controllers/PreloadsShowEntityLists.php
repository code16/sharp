<?php

namespace Code16\Sharp\Http\Controllers;

use Code16\Sharp\Data\Show\Fields\ShowEntityListFieldData;
use Code16\Sharp\Data\Show\ShowData;
use Code16\Sharp\Http\Middleware\AddLinkHeadersForPreloadedRequests;

trait PreloadsShowEntityLists
{
    protected function preloadShowEntityLists(ShowData $payload): void
    {
        $payload->fields->whereInstanceOf(ShowEntityListFieldData::class)
            ->each(function (ShowEntityListFieldData $entityListField) use ($payload) {
                $section = $payload->layout->sections->firstWhere(fn ($section) =>
                    collect(data_get($section->columns, '*.fields.*.*'))->firstWhere('key', $entityListField->key)
                );
                if(!$section->collapsable) {
                    app(AddLinkHeadersForPreloadedRequests::class)->preload($entityListField->endpointUrl);
                }
            });
    }
}
