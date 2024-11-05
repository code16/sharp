<?php

namespace Code16\Sharp\Form\Layout;

use Closure;
use Code16\Sharp\Utils\Layout\LayoutField;
use Illuminate\Support\Traits\Conditionable;

trait HasFieldRows
{
    use Conditionable;

    protected array $rows = [];

    /** @deprecated use withField() or withListField() instead */
    public function withSingleField(string $fieldKey, ?\Closure $subLayoutCallback = null): self
    {
        if ($subLayoutCallback) {
            return $this->withListField($fieldKey, $subLayoutCallback);
        }

        return $this->withField($fieldKey);
    }

    public function withField(string $fieldKey): self
    {
        $this->addRowLayout([
            $this->newLayoutField($fieldKey),
        ]);

        return $this;
    }

    public function withListField(string $fieldKey, Closure $subLayoutCallback): self
    {
        $this->addRowLayout([
            $this->newLayoutField($fieldKey, $subLayoutCallback),
        ]);

        return $this;
    }

    public function withFields(string ...$fieldKeys): self
    {
        $this
            ->addRowLayout(
                collect($fieldKeys)
                    ->map(fn ($key) => $this->newLayoutField($key))
                    ->all(),
            );

        return $this;
    }

    public function insertSingleFieldAt(int $index, string $fieldKey, ?\Closure $subLayoutCallback = null): self
    {
        $rows = collect($this->rows);
        $rows->splice($index, 0, [[$this->newLayoutField($fieldKey, $subLayoutCallback)]]);
        $this->rows = $rows->values()->all();

        return $this;
    }

    public function insertFieldsAt(int $index, string ...$fieldKeys): self
    {
        $rows = collect($this->rows);
        $rows->splice($index, 0, [
            collect($fieldKeys)
                ->map(fn ($key) => $this->newLayoutField($key))
                ->all(),
        ]);
        $this->rows = $rows->values()->all();

        return $this;
    }

    private function addRowLayout(array $fields): void
    {
        $this->rows[] = $fields;
    }

    public function fieldsToArray(): array
    {
        return [
            'fields' => collect($this->rows)
                ->map(fn ($row) => collect($row)
                    ->map(fn ($field) => $field->toArray())
                    ->all()
                )
                ->all(),
        ];
    }

    public function hasFields(): bool
    {
        return collect($this->rows)
            ->firstWhere(fn ($row) => count($row) > 0) !== null;
    }

    protected function newLayoutField(string $fieldKey, ?\Closure $subLayoutCallback = null): LayoutField
    {
        return new FormLayoutField($fieldKey, $subLayoutCallback);
    }
}
