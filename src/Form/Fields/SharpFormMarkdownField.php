<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithPlaceholder;

class SharpFormMarkdownField extends SharpFormField
{
    use SharpFormFieldWithPlaceholder;

    /**
     * @var int
     */
    protected $height;

    /**
     * @var array
     */
    protected $toolbar = [
        "bold", "italic", "|", "unordered-list", "ordered-list", "|", "link"
    ];

    /**
     * @var bool
     */
    protected $showToolbar = true;

    /**
     * @var int
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
     * @param int $sizeInMB
     * @return static
     */
    public function setMaxImageSize(int $sizeInMB)
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