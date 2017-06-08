<?php

namespace Code16\Sharp\Form\Fields;

class SharpFormTagsField extends SharpFormField
{
    /**
     * @var bool
     */
    protected $creatable = false;

    /**
     * @var string
     */
    protected $createText = "New...";

    /**
     * @var int
     */
    protected $maxTagCount = null;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @param string $key
     * @param array $options
     * @return static
     */
    public static function make(string $key, array $options)
    {
        $instance = new static($key, 'tags');
        $instance->options = collect($options)->map(function($label, $id) {
            return [
                "id" => $id, "label" => $label
            ];
        })->values()->all();

        return $instance;
    }

    /**
     * @param bool $creatable
     * @return static
     */
    public function setCreatable(bool $creatable = true)
    {
        $this->creatable = $creatable;

        return $this;
    }

    /**
     * @param string $createText
     * @return static
     */
    public function setCreateText(string $createText)
    {
        $this->createText = $createText;

        return $this;
    }

    /**
     * @param int $maxTagCount
     * @return static
     */
    public function setMaxTagCount(int $maxTagCount)
    {
        $this->maxTagCount = $maxTagCount;

        return $this;
    }

    /**
     * @return array
     */
    protected function validationRules()
    {
        return [
            "options" => "required|array",
            "creatable" => "boolean",
            "maxTagCount" => "integer",
        ];
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return parent::buildArray([
            "creatable" => $this->creatable,
            "createText" => $this->createText,
            "maxTagCount" => $this->maxTagCount,
            "options" => $this->options
        ]);
    }
}