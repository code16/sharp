<?php

namespace Code16\Sharp\EntityList\Fields;

class EntityListFilterField implements IsEntityListField
{
    use HasCommonEntityListFieldAttributes;

    private function __construct(protected string $filterKey)
    {
    }

    public static function make(string $filterKeyOrClassName): static
    {
        $filterKey = class_exists($filterKeyOrClassName)
            ? tap(app($filterKeyOrClassName), fn ($filter) => $filter->buildFilterConfig())->getKey()
            : $filterKeyOrClassName;

        return new static($filterKey);
    }

    public function getFieldProperties(): array
    {
        return [
            'type' => 'filter',
            'key' => $this->filterKey,
            'label' => $this->label,
            'sortable' => $this->sortable,
            'width' => $this->width,
            'hideOnXS' => $this->hideOnXs,
        ];
    }
}
