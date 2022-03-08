<?php

namespace Code16\Sharp\View\Components\Form;

use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteField;
use Illuminate\Support\Str;

class AutocompleteField extends Field
{
    protected SharpFormAutocompleteField $field;

    public function __construct(
        public string $name,
        public string $mode,
        public ?string $label = null,
        public ?bool $localized = null,
        public ?string $placeholder = null,
        public ?array $localSearchKeys = null,
        public ?string $remoteMethod = null,
        public ?string $remoteEndpoint = null,
        public ?string $remoteSearchAttribute = null,
        public ?string $itemIdAttribute = null,
        public ?int $searchMinChars = null,
        public ?string $dataWrapper = null,
        public ?int $debounceDelay = null,
    ) {
    }

    public function makeField(): SharpFormField
    {
        return SharpFormAutocompleteField::make($this->name, $this->mode);
    }
    
    public function setRemoteMethod()
    {
        if(Str::lower($this->remoteMethod) === 'post') {
            $this->field->setRemoteMethodPOST();
        }
    }
    
    public function setDebounceDelay()
    {
        $this->field->setDebounceDelayInMilliseconds($this->debounceDelay);
    }
}
