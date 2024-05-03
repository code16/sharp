<?php

namespace Code16\Sharp\EntityList\Fields;

class EntityListFilterField implements IsEntityListField
{
    use HasCommonEntityListFieldAttributes;

    protected string $filterKey;
    protected string $filterLabelAttribute = 'label';

    private function __construct(string $key, string $filterKey)
    {
        $this->key = $key;
        $this->filterKey = $filterKey;
    }

    public function setLabelAttribute(string $labelAttribute): self
    {
        $this->filterLabelAttribute = $labelAttribute;

        return $this;
    }

    public static function make(string $key, string $filterKeyOrClassName): static
    {
        $filterKey = class_exists($filterKeyOrClassName)
            ? tap(app($filterKeyOrClassName), fn ($filter) => $filter->buildFilterConfig())->getKey()
            : $filterKeyOrClassName;

        return new static($key, $filterKey);
    }

    public function getFieldProperties(): array
    {
        return [
            'type' => 'filter',
            'key' => $this->key,
            'filterKey' => $this->filterKey,
            'filterLabelAttribute' => $this->filterLabelAttribute,
            'label' => $this->label,
            'sortable' => $this->sortable,
            'width' => $this->width,
            'hideOnXS' => $this->hideOnXs,
        ];
    }
}
