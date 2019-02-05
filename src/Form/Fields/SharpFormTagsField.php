<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Formatters\TagsFormatter;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithDataLocalization;

class SharpFormTagsField extends SharpFormField
{
    use SharpFormFieldWithDataLocalization;

    const FIELD_TYPE = "tags";

    /**
     * @var bool
     */
    protected $creatable = false;

    /**
     * @var string
     */
    protected $createText = "Create";

    /**
     * @var string
     */
    protected $createAttribute = null;

    /**
     * @var string
     */
    protected $idAttribute = "id";

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
        $instance = new static($key, static::FIELD_TYPE, new TagsFormatter);

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
     * @param string $attribute
     * @return static
     */
    public function setCreateAttribute(string $attribute)
    {
        $this->createAttribute = $attribute;

        return $this;
    }

    /**
     * @param string $idAttribute
     * @return static
     */
    public function setIdAttribute($idAttribute)
    {
        $this->idAttribute = $idAttribute;

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
     * @return static
     */
    public function setMaxTagCountUnlimited()
    {
        $this->maxTagCount = null;

        return $this;
    }

    /**
     * @return bool
     */
    public function creatable()
    {
        return $this->creatable;
    }

    /**
     * @return string
     */
    public function createAttribute()
    {
        return $this->createAttribute;
    }

    /**
     * @return string
     */
    public function idAttribute()
    {
        return $this->idAttribute;
    }

    /**
     * @return array
     */
    public function options()
    {
        return $this->options;
    }

    /**
     * @return array
     */
    protected function validationRules()
    {
        return [
            "options" => "array",
            "creatable" => "boolean",
            "maxTagCount" => "integer",
        ];
    }

    /**
     * @return array
     * @throws \Code16\Sharp\Exceptions\Form\SharpFormFieldValidationException
     */
    public function toArray(): array
    {
        return parent::buildArray([
            "creatable" => $this->creatable,
            "createText" => $this->createText,
            "maxTagCount" => $this->maxTagCount,
            "options" => $this->options,
            "localized" => $this->localized,
        ]);
    }
}