<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Formatters\AutocompleteFormatter;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithDataLocalization;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithOptions;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithPlaceholder;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithTemplates;
use Illuminate\Support\Collection;

class SharpFormAutocompleteField extends SharpFormField
{
    use SharpFormFieldWithPlaceholder;
    use SharpFormFieldWithTemplates;
    use SharpFormFieldWithOptions;
    use SharpFormFieldWithDataLocalization;

    const FIELD_TYPE = 'autocomplete';

    protected string $mode;
    /** @var Collection|array */
    protected $localValues = [];
    protected array $localSearchKeys = ['value'];
    protected string $remoteMethod = 'GET';
    protected ?string $remoteEndpoint = null;
    protected string $remoteSearchAttribute = 'query';
    protected string $itemIdAttribute = 'id';
    protected int $searchMinChars = 1;
    protected ?array $dynamicAttributes = null;
    protected string $dataWrapper = '';
    protected int $debounceDelay = 300;

    /**
     * @param  string  $key
     * @param  string  $mode  "local" or "remote"
     * @return static
     */
    public static function make(string $key, string $mode): self
    {
        $instance = new static($key, static::FIELD_TYPE, new AutocompleteFormatter());
        $instance->mode = $mode;

        return $instance;
    }

    /**
     * @param  array|Collection  $localValues
     * @return $this
     */
    public function setLocalValues($localValues): self
    {
        $this->localValues = $localValues;

        return $this;
    }

    public function setLocalSearchKeys(array $localSearchKeys): self
    {
        $this->localSearchKeys = $localSearchKeys;

        return $this;
    }

    public function setRemoteEndpoint(string $remoteEndpoint): self
    {
        $this->remoteEndpoint = $remoteEndpoint;

        return $this;
    }

    public function setDynamicRemoteEndpoint(string $dynamicRemoteEndpoint, array $defaultValues = []): self
    {
        $this->remoteEndpoint = $dynamicRemoteEndpoint;

        if ($defaultValues) {
            $defaultEndpoint = $dynamicRemoteEndpoint;
            collect($defaultValues)
                ->each(function ($value, $name) use (&$defaultEndpoint) {
                    $defaultEndpoint = str_replace('{{'.$name.'}}', $value, $defaultEndpoint);
                });
        }

        $this->dynamicAttributes = [
            array_merge(
                [
                    'name' => 'remoteEndpoint',
                    'type' => 'template',
                ],
                ($defaultEndpoint ?? false)
                    ? ['default' => $defaultEndpoint]
                    : [],
            ),
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

    public function setItemIdAttribute(string $itemIdAttribute): self
    {
        $this->itemIdAttribute = $itemIdAttribute;

        return $this;
    }

    public function setListItemTemplatePath(string $listItemTemplatePath): self
    {
        $this->setTemplatePath($listItemTemplatePath, 'list');

        return $this;
    }

    public function setResultItemTemplatePath(string $resultItemTemplate): self
    {
        $this->setTemplatePath($resultItemTemplate, 'result');

        return $this;
    }

    public function setListItemInlineTemplate(string $template): self
    {
        return $this->setInlineTemplate($template, 'list');
    }

    public function setResultItemInlineTemplate(string $template): self
    {
        return $this->setInlineTemplate($template, 'result');
    }

    public function setAdditionalTemplateData(array $data): self
    {
        return $this->setTemplateData($data);
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

    public function isRemote(): bool
    {
        return $this->mode == 'remote';
    }

    public function isLocal(): bool
    {
        return $this->mode == 'local';
    }

    public function itemIdAttribute(): string
    {
        return $this->itemIdAttribute;
    }

    protected function validationRules(): array
    {
        return [
            'mode' => 'required|in:local,remote',
            'itemIdAttribute' => 'required',
            'listItemTemplate' => 'required',
            'resultItemTemplate' => 'required',
            'searchMinChars' => 'required|integer',
            'localValues' => 'array',
            'templateData' => 'nullable|array',
            'searchKeys' => 'required_if:mode,local|array',
            'remoteEndpoint' => 'required_if:mode,remote',
            'remoteMethod' => 'required_if:mode,remote|in:GET,POST',
            'remoteSearchAttribute' => 'required_if:mode,remote',
            'debounceDelay' => 'required|integer',
        ];
    }

    public function toArray(): array
    {
        return parent::buildArray(
            array_merge(
                [
                    'mode' => $this->mode,
                    'placeholder' => $this->placeholder,
                    'localValues' => $this->isLocal() && $this->dynamicAttributes
                        ? self::formatDynamicOptions($this->localValues, count($this->dynamicAttributes[0]['path']))
                        : ($this->isLocal() ? self::formatOptions($this->localValues, $this->itemIdAttribute) : []),
                    'itemIdAttribute' => $this->itemIdAttribute,
                    'searchKeys' => $this->localSearchKeys,
                    'remoteEndpoint' => $this->remoteEndpoint,
                    'dataWrapper' => $this->dataWrapper,
                    'remoteMethod' => $this->remoteMethod,
                    'remoteSearchAttribute' => $this->remoteSearchAttribute,
                    'debounceDelay' => $this->debounceDelay,
                    'templateData' => $this->additionalTemplateData,
                    'listItemTemplate' => $this->template('list'),
                    'resultItemTemplate' => $this->template('result'),
                    'searchMinChars' => $this->searchMinChars,
                    'localized' => $this->localized,
                ],
                $this->dynamicAttributes
                    ? ['dynamicAttributes' => $this->dynamicAttributes]
                    : [],
            ),
        );
    }
}
