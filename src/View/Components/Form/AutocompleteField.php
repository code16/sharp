<?php

namespace Code16\Sharp\View\Components\Form;

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
        public $localValues = null,
        public ?string $remoteMethod = null,
        public ?string $remoteEndpoint = null,
        public ?string $remoteSearchAttribute = null,
        public ?string $itemIdAttribute = null,
        public ?int $searchMinChars = null,
        public ?string $dataWrapper = null,
        public ?int $debounceDelay = null,
        public ?string $listItemTemplatePath = null,
        public ?string $resultItemTemplatePath = null,
    ) {
        $this->field = SharpFormAutocompleteField::make($this->name, $this->mode);

        if (Str::lower($this->remoteMethod) === 'post') {
            $this->field->setRemoteMethodPOST();
        }

        if ($this->debounceDelay) {
            $this->field->setDebounceDelayInMilliseconds($this->debounceDelay);
        }
    }

    public function updateFromSlots(array $slots)
    {
        if ($template = $slots['list_item'] ?? null) {
            $this->field->setListItemInlineTemplate($template);
        }

        if ($template = $slots['result_item'] ?? null) {
            $this->field->setResultItemInlineTemplate($template);
        }
    }
}
