<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithPlaceholder;
use Illuminate\Support\Collection;

class SharpFormAutocompleteField extends SharpFormField
{
    use SharpFormFieldWithPlaceholder;

    /**
     * @var string
     */
    protected $mode;

    /**
     * @var Collection|array
     */
    protected $localValues;

    /**
     * @var array
     */
    protected $searchKeys = ["value"];

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
     * @var string
     */
    protected $listItemTemplate;

    /**
     * @var string
     */
    protected $resultItemTemplate;

    /**
     * @var int
     */
    protected $searchMinChars = 1;

    /**
     * @param string $key
     * @param string $mode
     * @return static
     */
    public static function make(string $key, string $mode)
    {
        $instance = new static($key, 'autocomplete');
        $instance->mode = $mode;

        return $instance;
    }

    /**
     * @param array|Collection $localValues
     * @return $this
     */
    public function setLocalValues($localValues)
    {
        $this->localValues = collect($localValues)->map(function($label, $id) {
            return [
                "id" => $id, "label" => $label
            ];
        })->values()->all();

        return $this;
    }

    /**
     * @param array $searchKeys
     * @return $this
     */
    public function setSearchKeys(array $searchKeys)
    {
        $this->searchKeys = $searchKeys;

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
         return $this->setListItemInlineTemplate(
             file_get_contents(resource_path("views/" . $listItemTemplatePath))
         );
    }

    /**
     * @param string $resultItemTemplate
     * @return $this
     */
    public function setResultItemTemplatePath(string $resultItemTemplate)
    {
        return $this->setResultItemInlineTemplate(
            file_get_contents(resource_path("views/" . $resultItemTemplate))
        );
    }

    /**
     * @param string $template
     * @return $this
     */
    public function setListItemInlineTemplate(string $template)
    {
        $this->listItemTemplate = $template;

        return $this;
    }

    /**
     * @param string $template
     * @return $this
     */
    public function setResultItemInlineTemplate(string $template)
    {
        $this->resultItemTemplate = $template;

        return $this;
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
            "localValues" => "required_if:mode,local|array",
            "searchKeys" => "required_if:mode,local|array",
            "remoteEndpoint" => "required_if:mode,remote",
            "remoteMethod" => "required_if:mode,remote|in:GET,POST",
            "remoteSearchAttribute" => "required_if:mode,remote",
        ];
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return parent::buildArray([
            "mode" => $this->mode,
            "placeholder" => $this->placeholder,
            "localValues" => $this->localValues,
            "searchKeys" => $this->searchKeys,
            "remoteEndpoint" => $this->remoteEndpoint,
            "remoteMethod" => $this->remoteMethod,
            "remoteSearchAttribute" => $this->remoteSearchAttribute,
            "itemIdAttribute" => $this->itemIdAttribute,
            "listItemTemplate" => $this->listItemTemplate,
            "resultItemTemplate" => $this->resultItemTemplate,
            "searchMinChars" => $this->searchMinChars,
        ]);
    }
}