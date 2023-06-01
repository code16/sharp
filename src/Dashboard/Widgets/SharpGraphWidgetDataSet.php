<?php

namespace Code16\Sharp\Dashboard\Widgets;

use Illuminate\Support\Collection;

class SharpGraphWidgetDataSet
{
    protected array $values;
    protected ?string $label = null;
    protected ?string $color = null;

    protected function __construct(array|Collection $values)
    {
        $this->values = $values instanceof Collection
            ? $values->toArray()
            : $values;
    }
    
    public static function make(array|Collection$values): SharpGraphWidgetDataSet
    {
        return new static($values);
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'data' => array_values($this->values),
            'labels' => array_keys($this->values),
        ]
            + ($this->label ? ['label' => $this->label] : [])
            + ($this->color ? ['color' => $this->color] : []);
    }
}
