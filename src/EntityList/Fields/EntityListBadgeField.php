<?php

namespace Code16\Sharp\EntityList\Fields;

use Illuminate\Contracts\Support\Arrayable;

class EntityListBadgeField implements Arrayable, IsEntityListField
{
    use HasCommonEntityListFieldAttributes;

    protected ?string $tooltip = null;

    private function __construct(string $key)
    {
        $this->key = $key;
    }

    public static function make(string $key): self
    {
        return new static($key);
    }

    public function setTooltip(string $tooltip): self
    {
        $this->tooltip = $tooltip;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'type' => 'badge',
            'key' => $this->key,
            'label' => $this->label,
            'sortable' => $this->sortable,
            'width' => $this->width,
            'hideOnXS' => $this->hideOnXs,
            'tooltip' => $this->tooltip,
        ];
    }
}
