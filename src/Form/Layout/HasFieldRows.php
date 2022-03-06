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
                    ->map(function ($key) {
                        return $this->newLayoutField($key);
                    })
                    ->all(),
            );

        return $this;
    }

    public function appendLastRowField(string $fieldKey)
    {
        if (count($this->rows)) {
            $this->rows[count($this->rows) - 1][] = $this->newLayoutField($fieldKey);
        } else {
            $this->withSingleField($fieldKey);
        }
    }

    private function addRowLayout(array $fields): void
    {
        $this->rows[] = $fields;
    }

    public function fieldsToArray(): array
    {
        return [
            'fields' => collect($this->rows)
                ->map(function ($row) {
                    return collect($row)
                        ->map(function ($field) {
                            return $field->toArray();
                        })
                        ->all();
                })
                ->all(),
        ];
    }

    protected function newLayoutField(string $fieldKey, \Closure $subLayoutCallback = null): LayoutField
    {
        return new FormLayoutField($fieldKey, $subLayoutCallback);
    }
}
