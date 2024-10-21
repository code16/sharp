<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Formatters\AutocompleteFormatter;
use Code16\Sharp\Form\Fields\Utils\IsSharpFormAutocompleteField;
use Code16\Sharp\Form\Fields\Utils\SharpFormAutocompleteCommonField;
use Code16\Sharp\Utils\Fields\IsSharpFieldWithLocalization;
use Illuminate\Support\Collection;

class SharpFormAutocompleteLocalField
    extends SharpFormField
    implements IsSharpFieldWithLocalization, IsSharpFormAutocompleteField
{
    use SharpFormAutocompleteCommonField;

    protected Collection|array $localValues = [];
    protected array $localSearchKeys = ['value'];

    public static function make(string $key): self
    {
        $instance = new static($key, static::FIELD_TYPE, new AutocompleteFormatter());
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
                    'localValues' => $this->isLocal() && $this->dynamicAttributes
                        ? self::formatDynamicOptions($this->localValues, count($this->dynamicAttributes[0]['path']))
                        : ($this->isLocal() ? self::formatOptions($this->localValues, $this->itemIdAttribute) : []),
                    'searchKeys' => $this->localSearchKeys,
                ],
            ),
        );
    }
}
