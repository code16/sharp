<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Utils\WithPlaceholder;

class SharpFormAutocompleteField extends SharpFormField
{
    use WithPlaceholder;

    /**
     * @var string
     */
    protected $mode;

    /**
     * @var array
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
     * @param array $localValues
     * @return $this
     */
    public function setLocalValues(array $localValues)
    {
        $this->localValues = $localValues;

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
     * @param string $listItemTemplate
     * @return $this
     */
    public function setListItemTemplate(string $listItemTemplate)
    {
        $this->listItemTemplate = $listItemTemplate;

        return $this;
    }

    /**
     * @param string $resultItemTemplate
     * @return $this
     */
    public function setResultItemTemplate(string $resultItemTemplate)
    {
        $this->resultItemTemplate = $resultItemTemplate;

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
    public function toArray(): array
    {
        return parent::makeArray([
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