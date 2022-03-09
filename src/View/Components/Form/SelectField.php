<?php

namespace Code16\Sharp\View\Components\Form;

use Code16\Sharp\Form\Fields\SharpFormAutocompleteField;
use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Illuminate\Support\Str;

class SelectField extends Field
{
    protected ?SharpFormSelectField $field = null;
    public self $selectFieldComponent;

    public function __construct(
        public string $name,
        public ?string $label = null,
        public ?bool $localized = null,
        public ?array $options = null,
        public ?bool $multiple = null,
        public ?bool $clearable = null,
        public ?int $maxSelected = null,
        public ?string $display = null,
        public ?string $idAttribute = null,
        public ?bool $inline = null,
        public ?bool $showSelectAll = null,
    ) {
        $this->field = SharpFormSelectField::make($this->name, $this->options ?? []);
        $this->selectFieldComponent = $this;
    
        if($this->display === 'dropdown') {
            $this->field->setDisplayAsDropdown();
        }
    }
    
    public function addOption(string|int $value, string $label)
    {
        $this->options ??= [];
        $this->options[] = [
            'id' => $value,
            'label' => $label,
        ];
    }
    
    protected function updateFromSlots(array $slots)
    {
        $this->field->setOptions($this->options);
    }
}
