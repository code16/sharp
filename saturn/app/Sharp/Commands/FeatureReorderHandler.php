<?php

namespace App\Sharp\Commands;

use App\Feature;
use Code16\Sharp\EntityList\Commands\ReorderHandler;

class FeatureReorderHandler implements ReorderHandler
{
    function reorder(array $ids): void
    {
        Feature::whereIn("id", $ids)
            ->get()
            ->each(function(Feature $feature) use ($ids) {
                $feature->order = array_search($feature->id, $ids) + 1;
                $feature->save();
            });
    }
}