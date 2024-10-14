<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Formatters\AutocompleteFormatter;
use Code16\Sharp\Form\Fields\Utils\IsSharpFormAutocompleteField;
use Code16\Sharp\Form\Fields\Utils\SharpFormAutocompleteCommonField;
use Code16\Sharp\Utils\Fields\IsSharpFieldWithLocalization;

class SharpFormAutocompleteRemoteField
    extends SharpFormField
    implements IsSharpFieldWithLocalization, IsSharpFormAutocompleteField
{
    use SharpFormAutocompleteCommonField;

    protected string $remoteMethod = 'GET';
    protected ?string $remoteEndpoint = null;
    protected string $remoteSearchAttribute = 'query';
    protected int $searchMinChars = 1;
    protected string $dataWrapper = '';
    protected int $debounceDelay = 300;

    public static function make(string $key): self
    {
        $instance = new static($key, static::FIELD_TYPE, new AutocompleteFormatter());
        $instance->mode = 'remote';

        return $instance;
    }

    public function setRemoteEndpoint(string $remoteEndpoint): self
    {
        $this->remoteEndpoint = $remoteEndpoint;

        return $this;
    }

    public function setDynamicRemoteEndpoint(string $dynamicRemoteEndpoint, array $defaultValues = []): self
    {
        $this->remoteEndpoint = $dynamicRemoteEndpoint;
        
        $this->dynamicAttributes = [
            [
                'name' => 'remoteEndpoint',
                'type' => 'template',
                'default' => $defaultValues
                    ? collect($defaultValues)
                        ->reduce(function ($endpoint, $value, $name) {
                            return str_replace('{{'.$name.'}}', $value, $endpoint);
                        }, $dynamicRemoteEndpoint)
                    : null,
            ],
        ];

        return $this;
    }

    public function setRemoteSearchAttribute(string $remoteSearchAttribute): self
    {
        $this->remoteSearchAttribute = $remoteSearchAttribute;

        return $this;
    }

    public function setRemoteMethodGET(): self
    {
        $this->remoteMethod = 'GET';

        return $this;
    }

    public function setRemoteMethodPOST(): self
    {
        $this->remoteMethod = 'POST';

        return $this;
    }

    public function setSearchMinChars(int $searchMinChars): self
    {
        $this->searchMinChars = $searchMinChars;

        return $this;
    }

    public function setDebounceDelayInMilliseconds(int $debounceDelay): self
    {
        $this->debounceDelay = $debounceDelay;

        return $this;
    }

    public function setDataWrapper(string $dataWrapper): self
    {
        $this->dataWrapper = $dataWrapper;

        return $this;
    }

    protected function validationRules(): array
    {
        return array_merge(
            $this->validationRulesBase(),
            [
                'remoteEndpoint' => 'required',
                'remoteMethod' => 'required|in:GET,POST',
                'remoteSearchAttribute' => 'required',
                'searchMinChars' => 'required|integer',
                'debounceDelay' => 'required|integer',
            ],
        );
    }

    public function toArray(): array
    {
        return parent::buildArray(
            array_merge(
                $this->toArrayBase(),
                [
                    'remoteEndpoint' => $this->remoteEndpoint,
                    'dataWrapper' => $this->dataWrapper,
                    'remoteMethod' => $this->remoteMethod,
                    'remoteSearchAttribute' => $this->remoteSearchAttribute,
                    'debounceDelay' => $this->debounceDelay,
                    'searchMinChars' => $this->searchMinChars,
                ],
            ),
        );
    }
}
