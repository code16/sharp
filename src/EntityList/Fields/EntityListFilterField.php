<?php

namespace Code16\Sharp\EntityList\Fields;

class EntityListFilterField implements IsEntityListField
{
    public function __construct(protected string $filterClassName, protected string $label)
    {
    }

    public function getFieldProperties(): array
    {
        return [
            'key' => '@filter-' . $this->filterClassName,
            'label' => $this->label,
            'sortable' => false,
            'html' => false,
            'width' => null,
            'hideOnXS' => false,
        ];
    }
}
