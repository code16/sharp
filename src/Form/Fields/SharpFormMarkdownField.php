<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Utils\WithPlaceholder;

class SharpFormMarkdownField extends SharpFormField
{
    use WithPlaceholder;

    /**
     * @var int
     */
    protected $height;

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
     * @return $this
     */
    public function setHeight(int $height)
    {
        $this->height = $height;

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
            "placeholder" => $this->placeholder,
        ]);
    }
}