<?php

namespace Code16\Sharp\View\Components\Form;

use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Layout\FormLayoutFieldset;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\View\Components\Col;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use Illuminate\View\ComponentSlot;

abstract class Field extends Component
{
    public string $name;
    public ?string $label = null;
    protected ?SharpForm $form = null;
    protected ?Col $parentColComponent = null;
    protected ?FormLayoutFieldset $fieldset = null;
    protected ?SharpFormListField $parentListField = null;
    protected ?DisplayIf $displayIfComponent = null;
    
    private function mount()
    {
        $this->form = view()->getConsumableComponentData('form');
        $this->fieldset = view()->getConsumableComponentData('fieldset');
        $this->parentListField = view()->getConsumableComponentData('listField');
        $this->parentColComponent = view()->getConsumableComponentData('colComponent');
        $this->displayIfComponent = view()->getConsumableComponentData('displayIfComponent');
    }

    private function subLayoutCallback(): ?callable
    {
        if (method_exists($this, 'setItemLayout')) {
            return fn ($column) => $this->setItemLayout($column);
        }

        return null;
    }
    
    protected function updateFromSlots(array $slots)
    {
    }
    
    private function updatePropertiesWithSlots(array $slots)
    {
        $publicProperties = $this->extractPublicProperties();
        
        collect($slots)
            ->mapWithKeys(fn ($value, $key) => [
                Str::camel($key) => $value
            ])
            ->each(function ($value, $key) use ($publicProperties) {
                if (array_key_exists($key, $publicProperties)) {
                    $this->{$key} = $value;
                }
            });
    }
    
    private function updateFieldWithProperties()
    {
        collect($this->extractPublicProperties())
            ->filter(fn ($value) => ! is_null($value))
            ->each(function ($value, $key) {
                $method = 'set'.ucfirst($key);
                if (method_exists($this->field, $method)) {
                    $this->field->{$method}($value);
                }
            });
    }
    
    private function registerLayout()
    {
        if ($this->parentColComponent) {
            if ($this->fieldset && ! $this->parentColComponent->inFieldset()) {
                $this->fieldset->withSingleField($this->name, $this->subLayoutCallback());
            } else {
                $this->parentColComponent->addField($this->name, $this->subLayoutCallback());
            }
        }
    }

    private function registerField()
    {
        if ($this->parentListField) {
            $this->parentListField->addItemField($this->field);
        } else {
            $this->form->fieldsContainer()->addField($this->field);
        }
    }

    public function render(): callable
    {
        $this->mount();
        $this->registerLayout();
        $this->displayIfComponent?->addConditionalDisplay($this->field);

        return function ($data) {
            $slots = collect($data)->whereInstanceOf(ComponentSlot::class)->all();
            $this->updateFromSlots($slots);
            $this->updatePropertiesWithSlots($slots);
            $this->updateFieldWithProperties();
            $this->registerField();
        };
    }
}
