<?php

namespace Code16\Sharp\View\Components\Form;

use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\View\Components\Col;
use Illuminate\View\Component;

abstract class Field extends Component
{
    public string $name;
    public ?string $label = null;
    public ?SharpForm $form = null;
    public ?Col $parentColComponent = null;

    abstract public function makeField(): SharpFormField;

    private function registerField()
    {
        $this->field = $this->makeField();

        collect($this->extractPublicProperties())
            ->filter(fn ($value) => ! is_null($value))
            ->each(function ($value, $key) {
                $method = 'set'.ucfirst($key);
                if (method_exists($this, $method)) {
                    $this->{$method}($value);
                } elseif (method_exists($this->field, $method)) {
                    $this->field->{$method}($value);
                }
            });

        $this->form->fieldsContainer()->addField($this->field);
        $this->parentColComponent?->addField($this->name);
    }

    private function updateProperties(array $viewData)
    {
        collect($this->extractPublicProperties())
            ->each(function ($value, $key) use ($viewData) {
                if (array_key_exists($key, $viewData)) {
                    $this->{$key} = $viewData[$key];
                }
            });
    }

    public function render(): callable
    {
        $this->form = view()->getConsumableComponentData('form');
        $this->parentColComponent = view()->getConsumableComponentData('colComponent');

        return function ($data) {
            $this->updateProperties($data);
            $this->registerField();
        };
    }
}
