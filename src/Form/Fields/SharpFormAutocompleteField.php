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
    use SharpFormFieldWithPlaceholder, SharpFormFieldWithTemplates,
        SharpFormFieldWithOptions, SharpFormFieldWithDataLocalization;

    const FIELD_TYPE = "autocomplete";

    /**
     * @var string
     */
    protected $mode;

    /**
     * @var Collection|array
     */
    protected $localValues = [];

    /**
     * @var array
     */
    protected $localSearchKeys = ["value"];

    /**
     * @var string
     */
    protected $remoteMethod = "GET";

    /**
     * @var string
     */
    protected $remoteEndpoint;

    /**
     * @var string
     */
    protected $remoteSearchAttribute = "query";

    /**
     * @var string
     */
    protected $itemIdAttribute = "id";

    /**
     * @var int
     */
    protected $searchMinChars = 1;

    /**
     * @var array
     */
    protected $dynamicAttributes;

    /**
     * @param string $key
     * @param string $mode "local" or "remote"
     * @return static
     */
    public static function make(string $key, string $mode)
    {
        $instance = new static($key, static::FIELD_TYPE, new AutocompleteFormatter());
        $instance->mode = $mode;

        return $instance;
    }

    /**
     * @param array|Collection $localValues
     * @return $this
     */
    public function setLocalValues($localValues)
    {
        $this->localValues = $localValues;

        return $this;
    }

    /**
     * @param array $localSearchKeys
     * @return $this
     */
    public function setLocalSearchKeys(array $localSearchKeys)
    {
        $this->localSearchKeys = $localSearchKeys;

        return $this;
    }

    /**
     * @param string $remoteEndpoint
     * @return $this
     */
    public function setRemoteEndpoint(string $remoteEndpoint)
    {
        $this->remoteEndpoint = $remoteEndpoint;

        return $this;
    }

    /**
     * @param string $dynamicRemoteEndpoint
     * @param array $defaultValues
     * @return $this
     */
    public function setDynamicRemoteEndpoint(string $dynamicRemoteEndpoint, array $defaultValues = [])
    {
        $this->remoteEndpoint = $dynamicRemoteEndpoint;

        if($defaultValues) {
            $defaultEndpoint = $dynamicRemoteEndpoint;
            collect($defaultValues)
                ->each(function ($value, $name) use (&$defaultEndpoint) {
                    $defaultEndpoint = str_replace("{{" . $name . "}}", $value, $defaultEndpoint);
                });
        }

        $this->dynamicAttributes = [
            array_merge(
                [
                    "name" => "remoteEndpoint",
                    "type"  => "template"
                ],
                ($defaultEndpoint ?? false)
                    ? ["default" => $defaultEndpoint]
                    : []
            )
        ];

        return $this;
    }

    /**
     * @param string $remoteSearchAttribute
     * @return $this
     */
    public function setRemoteSearchAttribute(string $remoteSearchAttribute)
    {
        $this->remoteSearchAttribute = $remoteSearchAttribute;

        return $this;
    }

    /**
     * @return $this
     */
    public function setRemoteMethodGET()
    {
        $this->remoteMethod = "GET";

        return $this;
    }

    /**
     * @return $this
     */
    public function setRemoteMethodPOST()
    {
        $this->remoteMethod = "POST";

        return $this;
    }

    /**
     * @param string $itemIdAttribute
     * @return $this
     */
    public function setItemIdAttribute(string $itemIdAttribute)
    {
        $this->itemIdAttribute = $itemIdAttribute;

        return $this;
    }

    /**
     * @param string $listItemTemplatePath
     * @return $this
     */
    public function setListItemTemplatePath(string $listItemTemplatePath)
    {
         return $this->setTemplatePath($listItemTemplatePath, "list");
    }

    /**
     * @param string $resultItemTemplate
     * @return $this
     */
    public function setResultItemTemplatePath(string $resultItemTemplate)
    {
        return $this->setTemplatePath($resultItemTemplate, "result");
    }

    /**
     * @param string $template
     * @return $this
     */
    public function setListItemInlineTemplate(string $template)
    {
        return $this->setInlineTemplate($template, "list");
    }

    /**
     * @param string $template
     * @return $this
     */
    public function setResultItemInlineTemplate(string $template)
    {
        return $this->setInlineTemplate($template, "result");
    }

    /**
     * @param int $searchMinChars
     * @return $this
     */
    public function setSearchMinChars(int $searchMinChars)
    {
        $this->searchMinChars = $searchMinChars;

        return $this;
    }

    /**
     * @param string ...$fieldKeys
     * @return $this
     */
    public function setLocalValuesLinkedTo(string ...$fieldKeys)
    {
        $this->dynamicAttributes = [
            [
                "name" => "localValues",
                "type" => "map",
                "path" => $fieldKeys
            ]
        ];

        return $this;
    }

    /**
     * @return bool
     */
    public function isRemote()
    {
        return $this->mode == "remote";
    }

    /**
     * @return bool
     */
    public function isLocal()
    {
        return $this->mode == "local";
    }

    /**
     * @return string
     */
    public function itemIdAttribute()
    {
        return $this->itemIdAttribute;
    }

    /**
     * @return array
     */
    protected function validationRules()
    {
        return [
            "mode" => "required|in:local,remote",
            "itemIdAttribute" => "required",
            "listItemTemplate" => "required",
            "resultItemTemplate" => "required",
            "searchMinChars" => "required|integer",
            "localValues" => "array",
            "searchKeys" => "required_if:mode,local|array",
            "remoteEndpoint" => "required_if:mode,remote",
            "remoteMethod" => "required_if:mode,remote|in:GET,POST",
            "remoteSearchAttribute" => "required_if:mode,remote",
        ];
    }

    /**
     * @return array
     * @throws \Code16\Sharp\Exceptions\Form\SharpFormFieldValidationException
     */
    public function toArray(): array
    {
        return parent::buildArray(
            array_merge([
                "mode" => $this->mode,
                "placeholder" => $this->placeholder,
                "localValues" => $this->isLocal() && $this->dynamicAttributes
                    ? self::formatDynamicOptions($this->localValues, count($this->dynamicAttributes[0]["path"]))
                    : ($this->isLocal() ? self::formatOptions($this->localValues, $this->itemIdAttribute) : []),
                "itemIdAttribute" => $this->itemIdAttribute,
                "searchKeys" => $this->localSearchKeys,
                "remoteEndpoint" => $this->remoteEndpoint,
                "remoteMethod" => $this->remoteMethod,
                "remoteSearchAttribute" => $this->remoteSearchAttribute,
                "listItemTemplate" => $this->template("list"),
                "resultItemTemplate" => $this->template("result"),
                "searchMinChars" => $this->searchMinChars,
                "localized" => $this->localized,
            ], $this->dynamicAttributes
                ? ["dynamicAttributes" => $this->dynamicAttributes]
                : []
            )
        );
    }
}