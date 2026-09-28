<?php

namespace Code16\Sharp\EntityList\Fields;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<array-key, mixed>
 */
class EntityListStateField implements Arrayable, IsEntityListField
{
    use HasCommonEntityListFieldAttributes;

    private function __construct() {}

    public static function make(): static
    {
        return new static();
    }

    public function toArray(): array
    {
        return [
            'type' => 'state',
            'key' => '@state',
            'label' => $this->label,
            'sortable' => $this->sortable,
            'width' => $this->width,
            'hideOnXS' => $this->hideOnXs,
        ];
    }
}
