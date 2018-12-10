<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Formatters\MarkdownFormatter;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithDataLocalization;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithPlaceholder;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithUpload;

class SharpFormMarkdownField extends SharpFormField
{
    use SharpFormFieldWithPlaceholder, SharpFormFieldWithUpload, SharpFormFieldWithDataLocalization;

    const FIELD_TYPE = "markdown";

    const B = "bold";
    const I = "italic";
    const UL = "unordered-list";
    const OL = "ordered-list";
    const SEPARATOR = "|";
    const A = "link";
    const H1 = "heading-1";
    const H2 = "heading-2";
    const H3 = "heading-3";
    const CODE = "code";
    const QUOTE = "quote";
    const IMG = "image";
    const HR = "horizontal-rule";

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
        return new static($key, static::FIELD_TYPE, new MarkdownFormatter());
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
            "height" => "integer",
            "toolbar" => "array|nullable",
            "maxImageSize" => "numeric",
            "ratioX" => "integer|nullable",
            "ratioY" => "integer|nullable",
            "croppableFileTypes" => "array|nullable",
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
            "localized" => $this->localized,
            "innerComponents" => [
                "upload" => $this->innerComponentUploadConfiguration()
            ]
        ]);
    }

    /**
     * @return array
     */
    protected function innerComponentUploadConfiguration()
    {
        $array = ["maxImageSize" => $this->maxFileSize ?: 2];

        if($this->cropRatio) {
            $array["ratioX"] = (int)$this->cropRatio[0];
            $array["ratioY"] = (int)$this->cropRatio[1];
            $array["croppableFileTypes"] = $this->croppableFileTypes;
        }

        return $array;
    }
}