<?php

namespace Code16\Sharp\Filters;

use Illuminate\Support\Arr;

abstract class AutocompleteRemoteMultipleFilter extends AutocompleteRemoteFilter
{
    private array $resolvedValues = [];
    private array $resolvedIds = [];

    public function fromQueryParam($value): array
    {
        $ids = $this->idsFromValue($value);
        $missingIds = collect($ids)
            ->reject(fn (string|int $id) => isset($this->resolvedIds[(string) $id]))
            ->values()
            ->all();

        if (count($missingIds)) {
            foreach ($missingIds as $id) {
                $this->resolvedIds[(string) $id] = true;
            }

            foreach ($this->format($this->valueLabelsFor($missingIds)) as $resolvedValue) {
                if (isset($resolvedValue['id'], $resolvedValue['label'])) {
                    $this->resolvedValues[(string) $resolvedValue['id']] = $resolvedValue;
                }
            }
        }

        return collect($ids)
            ->map(fn (string|int $id) => $this->resolvedValues[(string) $id] ?? null)
            ->filter()
            ->values()
            ->all();
    }

    public function toQueryParam($value): ?string
    {
        $ids = $this->idsFromValue($value);

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
        return $this->idsFromValue($value);
    }

    final public function valueLabelFor(string $id): ?string
    {
        return $this->fromQueryParam($id)[0]['label'] ?? null;
    }

    /**
     * @return array<int, string|int>
     */
    private function idsFromValue(mixed $value): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        if (is_array($value) && array_key_exists('id', $value)) {
            $value = [$value];
        } elseif (! is_array($value)) {
            $value = explode(',', (string) $value);
        }

        return collect(Arr::wrap($value))
            ->map(fn ($item) => is_array($item) ? ($item['id'] ?? null) : $item)
            ->filter(fn ($id) => is_string($id) || is_int($id))
            ->unique(fn (string|int $id) => (string) $id)
            ->values()
            ->all();
    }

    /**
     * Return labels for all requested IDs in one batch. The result may use either
     * the [id => label] or the [['id' => ..., 'label' => ...]] format.
     *
     * @param  array<int, string|int>  $ids
     * @return array<int|string, string|array{id: string|int, label: string}>
     */
    abstract public function valueLabelsFor(array $ids): array;
}
