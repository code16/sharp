<?php

namespace Code16\Sharp\Show\Fields;

use Code16\Sharp\Show\Fields\Formatters\ListFormatter;
use Illuminate\Support\Collection;

class SharpShowListField extends SharpShowField
{
    const FIELD_TYPE = 'list';

    protected ?string $label = null;
    protected array $itemFields = [];

    public static function make(string $key): SharpShowListField
    {
        return new static($key, static::FIELD_TYPE, new ListFormatter());
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function addItemField(SharpShowField $field): self
    {
        $this->itemFields[] = $field;

        return $this;
    }

    public function findItemFormFieldByKey(string $key): SharpShowField
    {
        return collect($this->itemFields)
            ->where('key', $key)
            ->first();
    }
    
    public function itemFields(): Collection
    {
        return collect($this->itemFields);
    }

    protected function validationRules(): array
    {
        return [
            'itemFields' => 'required|array',
        ];
    }

    public function toArray(): array
    {
        return parent::buildArray([
            'label' => $this->label,
            'itemFields' => collect($this->itemFields)
                ->map(fn (SharpShowField $field) => $field->toArray())
                ->keyBy('key')
                ->all(),
        ]);
    }
}
