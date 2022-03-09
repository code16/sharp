<?php

namespace Code16\Sharp\View\Components\Form;

use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\SharpFormTextField;

class TextField extends Field
{
    protected SharpFormTextField $field;

    public function __construct(
        public string $name,
        public ?string $label = null,
        public ?bool $localized = null,
        public ?string $placeholder = null,
        public ?string $type = null,
        public ?int $maxLength = null,
    ) {
        $this->field = SharpFormTextField::make($this->name);
    
        if ($this->type === 'password') {
            $this->field->setInputTypePassword();
        }
    }
}
