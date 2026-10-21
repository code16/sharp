<?php

namespace Code16\Sharp\Filters;

use Illuminate\Support\Arr;

abstract class AutocompleteRemoteMultipleFilter extends Filter
{
    use AutocompleteRemoteFilterTrait;

    public function fromQueryParam($value): array
    {
        if (! is_string($value)) {
            return [];
        }

        $ids = $this->formatIds(explode(',', $value));

        if (! count($ids)) {
            return [];
        }

        return once(function () use ($ids) {
            $labels = $this->valueLabelsFor($ids);

            return collect($ids)
                ->filter(fn (string|int $id) => isset($labels[$id]))
                ->map(fn (string|int $id) => [
                    'id' => $id,
                    'label' => $labels[$id],
                ])
                ->values()
                ->all();
        });
    }

    public function toQueryParam($value): ?string
    {
        $ids = $this->formatIds(Arr::wrap($value));

        return count($ids)
            ? implode(',', $ids)
            : null;
    }

    public function defaultValue(): array
    {
        return [];
    }

    public function formatRawValue(mixed $value): array
    {
        return $this->formatIds(Arr::wrap($value));
    }

    /**
     * @return array<int, string|int>
     */
    private function formatIds(array $value): array
    {
        return collect($value)
            ->map(fn ($item) => is_array($item) ? ($item['id'] ?? null) : $item)
            ->filter(fn ($id) => is_string($id) && trim($id) !== '' || is_int($id))
            ->unique(fn (string|int $id) => (string) $id)
            ->values()
            ->all();
    }

    /**
     * Return labels for all requested IDs in one batch, as [id => label].
     *
     * @param  array<int, string|int>  $ids
     * @return array<int|string, string>
     */
    abstract public function valueLabelsFor(array $ids): array;
}
