<?php

namespace Code16\Sharp\EntityList\Fields;

class EntityListStateField implements IsEntityListField
{
    public function __construct(protected string $label)
    {
    }

    public function getFieldProperties(): array
    {
        return [
            'key' => '@state',
            'label' => $this->label,
            'sortable' => false,
            'html' => false,
            'size' => 'fill',
            'hideOnXS' => false,
            'sizeXS' => 'fill',
        ];
    }
}
