<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithPlaceholder;

class SharpFormMarkdownField extends SharpFormField
{
    use SharpFormFieldWithPlaceholder;

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
     * @var float
     */
    protected $maxImageSize = 2;

    /**
     * @param string $key
     * @return static
     */
    public static function make(string $key)
    {
        return new static($key, 'markdown');
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
     * @param float $sizeInMB
     * @return static
     */
    public function setMaxImageSize(float $sizeInMB)
    {
        $this->maxImageSize = $sizeInMB;

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
        ];
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return parent::buildArray([
            "height" => $this->height,
            "toolbar" => $this->showToolbar ? $this->toolbar : null,
            "placeholder" => $this->placeholder,
            "maxImageSize" => $this->maxImageSize,
        ]);
    }
}