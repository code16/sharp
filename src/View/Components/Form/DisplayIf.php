<?php

namespace Code16\Sharp\View\Components\Form;

use Code16\Sharp\Form\Fields\SharpFormField;
use Illuminate\View\Component;

class DisplayIf extends Component
{
    public self $displayIfComponent;

    public function __construct(
        public string $field,
        public $equals,
    ) {
        $this->displayIfComponent = $this;
    }

    public function addConditionalDisplay(SharpFormField $field)
    {
        $field->addConditionalDisplay($this->field, $this->equals);
    }

    public function render(): callable
    {
        return function () {
        };
    }
}
