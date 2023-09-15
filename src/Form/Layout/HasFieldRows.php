<?php

namespace Code16\Sharp\Form\Layout;

use Code16\Sharp\Utils\Layout\LayoutField;

trait HasFieldRows
{
    protected array $rows = [];

    public function withSingleField(string $fieldKey, \Closure $subLayoutCallback = null): self
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

    public function insertSingleFieldAt(int $index, string $fieldKey, \Closure $subLayoutCallback = null): self
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
                ->firstWhere(fn ($row) => sizeof($row) > 0) !== null;
    }

    protected function newLayoutField(string $fieldKey, \Closure $subLayoutCallback = null): LayoutField
    {
        return new FormLayoutField($fieldKey, $subLayoutCallback);
    }
}
