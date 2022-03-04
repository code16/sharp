<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Formatters\WysiwygFormatter;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithDataLocalization;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithPlaceholder;

class SharpFormWysiwygField extends SharpFormField
{
    use SharpFormFieldWithPlaceholder;
    use SharpFormFieldWithDataLocalization;

    const FIELD_TYPE = 'wysiwyg';

    const B = 'bold';
    const I = 'italic';
    const A = 'link';
    const S = 'strike';

    const UL = 'bullet';
    const OL = 'number';
    const H1 = 'heading1';
    const CODE = 'code';
    const QUOTE = 'quote';
    const INCREASE_NESTING = 'increaseNestingLevel';
    const DECREASE_NESTING = 'decreaseNestingLevel';

    const UNDO = 'undo';
    const REDO = 'redo';
    const SEPARATOR = '|';

    protected ?int $height = null;
    protected array $toolbar = [
        self::B, self::I,
        self::SEPARATOR,
        self::UL,
        self::SEPARATOR,
        self::A,
    ];
    protected bool $showToolbar = true;

    public static function make(string $key): self
    {
        return new static($key, static::FIELD_TYPE, new WysiwygFormatter());
    }

    public function setHeight(int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function setToolbar(array $toolbar): self
    {
        $this->toolbar = $toolbar;

        return $this;
    }

    public function hideToolbar(): self
    {
        $this->showToolbar = false;

        return $this;
    }

    public function showToolbar(): self
    {
        $this->showToolbar = true;

        return $this;
    }

    protected function validationRules(): array
    {
        return [
            'height'  => 'integer|nullable',
            'toolbar' => 'array|nullable',
        ];
    }

    public function toArray(): array
    {
        return parent::buildArray([
            'height'      => $this->height,
            'toolbar'     => $this->showToolbar ? $this->toolbar : null,
            'placeholder' => $this->placeholder,
            'localized'   => $this->localized,
        ]);
    }
}
