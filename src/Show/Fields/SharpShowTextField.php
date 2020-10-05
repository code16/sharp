<?php

namespace Code16\Sharp\Show\Fields;

class SharpShowTextField extends SharpShowField
{
    const FIELD_TYPE = "text";

    /** @var string */
    protected $label = null;

    /** @var int */
    protected $collapseToWordCount = null;

    /**
     * @param string $key
     * @return static
     */
    public static function make(string $key)
    {
        return new static($key, static::FIELD_TYPE);
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
     * @param int $wordCount
     * @return $this
     */
    public function collapseToWordCount(int $wordCount)
    {
        $this->collapseToWordCount = $wordCount;

        return $this;
    }

    /**
     * @return $this
     */
    public function doNotCollapse()
    {
        $this->collapseToWordCount = null;

        return $this;
    }

    /**
     * Create the properties array for the field, using parent::buildArray()
     *
     * @return array
     * @throws \Code16\Sharp\Exceptions\Show\SharpShowFieldValidationException
     */
    public function toArray(): array
    {
        return parent::buildArray([
            "label" => $this->label,
            "collapseToWordCount" => $this->collapseToWordCount,
        ]);
    }

    /**
     * @return array
     */
    protected function validationRules()
    {
        return [
            "collapseToWordCount" => "int|nullable"
        ];
    }
}