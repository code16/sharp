<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Formatters\WysiwygFormatter;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithDataLocalization;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithPlaceholder;

class SharpFormWysiwygField extends SharpFormField
{
    use SharpFormFieldWithPlaceholder, SharpFormFieldWithDataLocalization;

    const FIELD_TYPE = "wysiwyg";

    const B = "bold";
    const I = "italic";
    const A = "link";
    const S = "strike";

    const UL = "bullet";
    const OL = "number";
    const H1 = "heading1";
    const CODE = "code";
    const QUOTE = "quote";
    const INCREASE_NESTING = "increaseNestingLevel";
    const DECREASE_NESTING = "decreaseNestingLevel";

    const UNDO = "undo";
    const REDO = "redo";
    const SEPARATOR = "|";

    /**
     * @var int
     */
    protected $height;

    /**
     * @var array
     */
    protected $toolbar = [
        self::B, self::I,
        self::SEPARATOR,
        self::UL,
        self::SEPARATOR,
        self::A,
    ];

    /**
     * @var bool
     */
    protected $showToolbar = true;

    /**
     * @param string $key
     * @return static
     */
    public static function make(string $key)
    {
        return new static($key, static::FIELD_TYPE, new WysiwygFormatter());
    }

    /**
     * @param int $height
     * @return static
     */
    public function setHeight(int $height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @param array $toolbar
     * @return static
     */
    public function setToolbar(array $toolbar)
    {
        $this->toolbar = $toolbar;

        return $this;
    }

    /**
     * @return static
     */
    public function hideToolbar()
    {
        $this->showToolbar = false;

        return $this;
    }

    /**
     * @return static
     */
    public function showToolbar()
    {
        $this->showToolbar = true;

        return $this;
    }

    /**
     * @return array
     */
    protected function validationRules()
    {
        return [
            "height" => "integer|nullable",
            "toolbar" => "array|nullable",
        ];
    }

    /**
     * @return array
     * @throws \Code16\Sharp\Exceptions\Form\SharpFormFieldValidationException
     */
    public function toArray(): array
    {
        return parent::buildArray([
            "height" => $this->height,
            "toolbar" => $this->showToolbar ? $this->toolbar : null,
            "placeholder" => $this->placeholder,
            "localized" => $this->localized
        ]);
    }
}