<?php

namespace Code16\Sharp\EntitiesList\containers;

class EntitiesListDataContainer
{

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $label = "";

    /**
     * @var boolean
     */
    protected $sortable = false;

    /**
     * @var boolean
     */
    protected $html = false;

    /**
     * @param string $key
     * @return static
     */
    public static function make(string $key)
    {
        $instance = new static();
        $instance->key = $key;

        return $instance;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel(string $label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @param bool $sortable
     * @return $this
     */
    public function setSortable(bool $sortable = true)
    {
        $this->sortable = $sortable;

        return $this;
    }

    /**
     * @param bool $html
     * @return $this
     */
    public function setHtml(bool $html = true)
    {
        $this->html = $html;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            "key" => $this->key,
            "label" => $this->label,
            "sortable" => $this->sortable,
            "html" => $this->html,
        ];
    }
}