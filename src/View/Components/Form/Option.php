<?php

namespace Code16\Sharp\View\Components\Form;


use Illuminate\View\Component;

class Option extends Component
{
    public Select $selectFieldComponent;

    public function __construct(
        public string|int $value,
        public ?string $label = null,
    ) {
        $this->selectFieldComponent = view()->getConsumableComponentData('selectFieldComponent');
    }
    
    public function render(): callable
    {
        return function ($data) {
            $this->label ??= $data['slot'];
            $this->selectFieldComponent->addOption($this->value, $this->label);
        };
    }
}
