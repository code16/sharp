<?php

namespace App\Sharp;

use App\Feature;
use App\Sharp\Commands\FeatureReorderHandler;
use Code16\Sharp\EntityList\Containers\EntityListDataContainer;
use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\SharpEntityList;

class FeatureSharpList extends SharpEntityList
{

    function buildListDataContainers(): void
    {
        $this
            ->addDataContainer(
                EntityListDataContainer::make("name")
                    ->setLabel("Name")
            )
            ->addDataContainer(
                EntityListDataContainer::make("type")
                    ->setLabel("Type")
            );
    }

    function buildListConfig(): void
    {
        $this->setReorderable(new FeatureReorderHandler());
    }

    function buildListLayout(): void
    {
        $this
            ->addColumn("name", 6)
            ->addColumn("type", 6);
    }

    function getListData(): array
    {
        return $this
            ->setCustomTransformer("type", function($value, $instance) {
                return (Feature::TYPES[$instance->type] ?? "?")
                    . " / "
                    . (Feature::SUBTYPES[$instance->type][$instance->subtype] ?? "?");
            })
            ->transform(
                Feature::orderBy('order', 'asc')->get()
            );
    }
}