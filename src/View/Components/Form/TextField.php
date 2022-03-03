<?php

namespace Code16\Sharp\View\Components\Form;

use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\SharpForm;
use Illuminate\View\Component;

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
    }
    
    public function makeField(): SharpFormField
    {
        return SharpFormTextField::make($this->name);
    }
    
    public function setType()
    {
        if($this->type === 'password') {
            $this->field->setInputTypePassword();
        }
    }
}
