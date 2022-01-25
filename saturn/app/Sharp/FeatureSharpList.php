<?php

namespace App\Sharp;

use App\Feature;
use App\Sharp\Commands\FeatureReorderHandler;
use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\EntityList\Fields\EntityListFieldsLayout;
use Code16\Sharp\EntityList\SharpEntityList;

class FeatureSharpList extends SharpEntityList
{
    public function buildListFields(EntityListFieldsContainer $fieldsContainer): void
    {
        $fieldsContainer
            ->addField(
                EntityListField::make('name')
                    ->setLabel('Name'),
            )
            ->addField(
                EntityListField::make('type')
                    ->setLabel('Type'),
            );
    }

    public function buildListConfig(): void
    {
        $this->configureReorderable(new FeatureReorderHandler());
    }

    public function buildListLayout(EntityListFieldsLayout $fieldsLayout): void
    {
        $fieldsLayout
            ->addColumn('name', 6)
            ->addColumn('type', 6);
    }

    public function getListData(): array
    {
        return $this
            ->setCustomTransformer('type', function ($value, $instance) {
                return (Feature::TYPES[$instance->type] ?? '?')
                    .' / '
                    .(Feature::SUBTYPES[$instance->type][$instance->subtype] ?? '?');
            })
            ->transform(
                Feature::orderBy('order', 'asc')->get(),
            );
    }
}
