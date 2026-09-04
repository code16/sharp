<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Formatters\TextFormatter;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithMaxLength;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithPlaceholder;
use Code16\Sharp\Utils\Fields\IsSharpFieldWithLocalization;
use Code16\Sharp\Utils\Fields\SharpFieldWithLocalization;
use Code16\Sharp\Utils\Sanitization\IsSharpFieldWithHtmlSanitization;
use Code16\Sharp\Utils\Sanitization\SharpFieldWithHtmlSanitization;

class SharpFormTextField extends SharpFormField implements IsSharpFieldWithHtmlSanitization, IsSharpFieldWithLocalization
{
    use SharpFieldWithHtmlSanitization;
    use SharpFieldWithLocalization;
    use SharpFormFieldWithMaxLength;
    use SharpFormFieldWithPlaceholder;

    const FIELD_TYPE = 'text';

    protected string $inputType = 'text';
    private ?array $suggestions = null;

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

    public function setSuggestions(?array $suggestions = null): self
    {
        $this->suggestions = $suggestions;

        return $this;
    }

    protected function validationRules(): array
    {
        return [
            'inputType' => 'required|in:text,password,email,tel,url',
            'suggestions' => 'nullable|array',
            'suggestions.*' => 'string',
        ];
    }

    public function toArray(): array
    {
        return parent::buildArray([
            'inputType' => $this->inputType,
            'placeholder' => $this->placeholder,
            'maxLength' => $this->maxLength,
            'localized' => $this->localized,
            'suggestions' => $this->suggestions,
        ]);
    }
}
