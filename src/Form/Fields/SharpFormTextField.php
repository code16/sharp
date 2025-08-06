<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Formatters\TextFormatter;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithHtmlSanitization;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithMaxLength;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithPlaceholder;
use Code16\Sharp\Utils\Fields\IsSharpFieldWithLocalization;
use Code16\Sharp\Utils\Fields\SharpFieldWithLocalization;

class SharpFormTextField extends SharpFormField implements IsSharpFieldWithLocalization
{
    use SharpFieldWithLocalization;
    use SharpFormFieldWithHtmlSanitization;
    use SharpFormFieldWithMaxLength;
    use SharpFormFieldWithPlaceholder;

    const FIELD_TYPE = 'text';

    protected string $inputType = 'text';

    public static function make(string $key): self
    {
        return new static($key, static::FIELD_TYPE, new TextFormatter());
    }

    public function setInputTypeText(): self
    {
        $this->inputType = 'text';

        return $this;
    }

    public function setInputTypePassword(): self
    {
        $this->inputType = 'password';

        return $this;
    }

    public function setInputTypeEmail(): self
    {
        $this->inputType = 'email';

        return $this;
    }

    public function setInputTypeTel(): self
    {
        $this->inputType = 'tel';

        return $this;
    }

    public function setInputTypeUrl(): self
    {
        $this->inputType = 'url';

        return $this;
    }

    protected function validationRules(): array
    {
        return [
            'inputType' => 'required|in:text,password,email,tel,url',
        ];
    }

    public function toArray(): array
    {
        return parent::buildArray([
            'inputType' => $this->inputType,
            'placeholder' => $this->placeholder,
            'maxLength' => $this->maxLength,
            'localized' => $this->localized,
        ]);
    }
}
