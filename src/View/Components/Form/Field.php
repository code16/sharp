<?php

namespace Code16\Sharp\View\Components\Form;

use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Layout\FormLayoutFieldset;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\View\Components\Col;
use Illuminate\View\Component;

abstract class Field extends Component
{
    public string $name;
    public ?string $label = null;
    protected ?SharpForm $form = null;
    protected ?Col $parentColComponent = null;
    protected ?FormLayoutFieldset $fieldset = null;
    protected ?SharpFormListField $parentListField = null;

    protected function updateFromSlots(array $slots)
    {
    }

    private function subLayoutCallback(): ?callable
    {
        if (method_exists($this, 'setItemLayout')) {
            return fn ($column) => $this->setItemLayout($column);
        }

        return null;
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

        if ($this->parentListField) {
            $this->parentListField->addItemField($this->field);
        } else {
            $this->form->fieldsContainer()->addField($this->field);
        }
    }

    public function render(): callable
    {
        $this->form = view()->getConsumableComponentData('form');
        $this->fieldset = view()->getConsumableComponentData('fieldset');
        $this->parentListField = view()->getConsumableComponentData('listField');
        $this->parentColComponent = view()->getConsumableComponentData('colComponent');

        if ($this->parentColComponent) {
            if ($this->fieldset && ! $this->parentColComponent->inFieldset()) {
                $this->fieldset->withSingleField($this->name, $this->subLayoutCallback());
            } else {
                $this->parentColComponent->addField($this->name, $this->subLayoutCallback());
            }
        }

        return function ($data) {
            $this->updateFromSlots($data);
            $this->registerField($data);
        };
    }
}
