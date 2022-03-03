<?php

namespace Code16\Sharp\View\Components\Form;

use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\SharpForm;
use Illuminate\View\Component;

abstract class Field extends Component
{
    protected SharpForm $form;
    public string $name;
    public ?string $label = null;
    
    abstract function makeField(): SharpFormField;
    
    function registerField(SharpForm $form)
    {
        $this->form = $form;
        $this->field = $this->makeField();
        
        collect($this->extractPublicProperties())
            ->filter(fn ($value) => !is_null($value))
            ->each(function ($value, $key) {
                $method = 'set' . ucfirst($key);
                if (method_exists($this, $method)) {
                    $this->{$method}($value);
                } else if (method_exists($this->field, $method)) {
                    $this->field->{$method}($value);
                }
            });
        
        $this->form->fieldsContainer()->addField($this->field);
    }
    
    public function render(): callable
    {
        return function ($data) {
            collect($this->extractPublicProperties())
                ->each(function ($value, $key) use ($data) {
                    if(array_key_exists($key, $data)) {
                        $this->{$key} = $data[$key];
                    }
                });
            
            return 'sharp::components.form.field';
        };
    }
}
