<?php

namespace Code16\Sharp\View\Components\Form;

use Code16\Sharp\Form\Layout\FormLayoutFieldset;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\View\Components\Col;
use Illuminate\View\Component;

abstract class Field extends Component
{
    public string $name;
    public ?string $label = null;
    public ?SharpForm $form = null;
    public ?Col $parentColComponent = null;
    public ?FormLayoutFieldset $fieldset = null;

    protected function updateFromSlots(array $slots)
    {
    }

    private function registerField(array $viewData)
    {
        collect($this->extractPublicProperties())
            ->each(function ($value, $key) use ($viewData) {
                if (array_key_exists($key, $viewData)) {
                    $this->{$key} = $viewData[$key];
                }
            });

        collect($this->extractPublicProperties())
            ->filter(fn ($value) => ! is_null($value))
            ->each(function ($value, $key) {
                $method = 'set'.ucfirst($key);
                if (method_exists($this->field, $method)) {
                    $this->field->{$method}($value);
                }
            });

        $this->form->fieldsContainer()->addField($this->field);
        if ($this->parentColComponent) {
            if ($this->fieldset && ! $this->parentColComponent->inFieldset()) {
                $this->fieldset->withSingleField($this->name);
            } else {
                $this->parentColComponent->addField($this->name);
            }
        }
    }

    public function render(): callable
    {
        $this->form = view()->getConsumableComponentData('form');
        $this->fieldset = view()->getConsumableComponentData('fieldset');
        $this->parentColComponent = view()->getConsumableComponentData('colComponent');

        return function ($data) {
            $this->updateFromSlots($data);
            $this->registerField($data);
        };
    }
}
