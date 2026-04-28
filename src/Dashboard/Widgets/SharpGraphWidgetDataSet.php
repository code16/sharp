<?php

namespace Code16\Sharp\Dashboard\Widgets;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class SharpGraphWidgetDataSet
{
    protected array $values;
    protected ?string $label = null;
    protected ?string $color = null;
    protected bool $hasDateLabels = false;

    protected function __construct(array|Collection $values)
    {
        $this->values = $values instanceof Collection
            ? $values->toArray()
            : $values;
    }

    public static function make(array|Collection $values): SharpGraphWidgetDataSet
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

    /**
     * @internal
     */
    public function withDateLabels(bool $hasDateLabels = true): self
    {
        $this->hasDateLabels = $hasDateLabels;

        return $this;
    }

    protected function formatValues(array $values): array
    {
        return array_map(fn ($value) => (float) $value, array_values($values));
    }

    protected function formatLabels(array $values): array
    {
        if ($this->hasDateLabels) {
            return array_map(function ($value) {
                return (new Carbon($value))->setTimezone(config('app.timezone'))->toAtomString();
            }, array_keys($values));
        }

        return array_keys($values);
    }

    public function toArray(): array
    {
        return collect()
            ->merge([
                'data' => $this->formatValues($this->values),
                'labels' => $this->formatLabels($this->values),
            ])
            ->when(
                $this->label,
                fn (Collection $collection) => $collection->merge(['label' => $this->label])
            )
            ->when(
                $this->color,
                fn (Collection $collection) => $collection->merge(['color' => $this->color])
            )
            ->all();
    }
}
