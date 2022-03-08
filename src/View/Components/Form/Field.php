<?php

namespace Code16\Sharp\View\Components\Form;

use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\View\Components\Col;
use Code16\Sharp\View\Components\Form;
use Code16\Sharp\View\Utils\InjectComponentData;
use Illuminate\View\Component;

abstract class Field extends Component
{
    use InjectComponentData;

    public string $name;
    public ?string $label = null;

    abstract public function makeField(): SharpFormField;

    public function registerField(
        Form $formComponent,
        ?Col $colComponent,
    ) {
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

        $formComponent->form->fieldsContainer()->addField($this->field);
        $colComponent?->addField($this->name);
    }

    public function render(): callable
    {
        return function ($data) {
            collect($this->extractPublicProperties())
                ->each(function ($value, $key) use ($data) {
                    if (array_key_exists($key, $data)) {
                        $this->{$key} = $data[$key];
                    }
                });

            $this->registerField(
                $this->aware('formComponent'),
                $this->aware('colComponent'),
            );
        };
    }
}
