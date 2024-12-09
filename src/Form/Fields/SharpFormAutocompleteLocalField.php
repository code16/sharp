<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Formatters\AutocompleteLocalFormatter;
use Code16\Sharp\Form\Fields\Utils\IsSharpFormAutocompleteField;
use Code16\Sharp\Form\Fields\Utils\SharpFormAutocompleteCommonField;
use Illuminate\Support\Collection;

class SharpFormAutocompleteLocalField extends SharpFormField implements IsSharpFormAutocompleteField
{
    use SharpFormAutocompleteCommonField;

    protected Collection|array $localValues = [];
    protected array $localSearchKeys = ['value'];

    public static function make(string $key): self
    {
        $instance = new static($key, static::FIELD_TYPE, new AutocompleteLocalFormatter());
        $instance->mode = 'local';

        return $instance;
    }

    public function setLocalValues(array|Collection $localValues): self
    {
        $this->localValues = $localValues;

        return $this;
    }

    public function setLocalSearchKeys(array $localSearchKeys): self
    {
        $this->localSearchKeys = $localSearchKeys;

        return $this;
    }

    public function setLocalValuesLinkedTo(string ...$fieldKeys): self
    {
        $this->dynamicAttributes = [
            [
                'name' => 'localValues',
                'type' => 'map',
                'path' => $fieldKeys,
            ],
        ];

        return $this;
    }

    public function localValues(): array
    {
        return $this->localValues;
    }

    protected function validationRules(): array
    {
        return array_merge(
            $this->validationRulesBase(),
            [
                'localValues' => 'array',
                'searchKeys' => 'required|array',
            ]
        );
    }

    public function toArray(): array
    {
        return parent::buildArray(
            array_merge(
                $this->toArrayBase(),
                [
                    'localValues' => $this->dynamicAttributes
                        ? self::formatDynamicOptions(
                            $this->localValues,
                            count($this->dynamicAttributes[0]['path']),
                            $this->itemIdAttribute,
                            $this->itemWithRenderedTemplates(...)
                        )
                        : self::formatOptions(
                            $this->localValues,
                            $this->itemIdAttribute,
                            $this->itemWithRenderedTemplates(...)
                        ),
                    'searchKeys' => $this->localSearchKeys,
                ],
            ),
        );
    }
}
