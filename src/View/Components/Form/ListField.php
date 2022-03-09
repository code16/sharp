<?php

namespace Code16\Sharp\View\Components\Form;

use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Utils\Layout\LayoutColumn;
use Illuminate\Support\Str;

class ListField extends Field
{
    protected SharpFormListField $field;
    public ?SharpFormListField $listField;
    public ?FormLayoutColumn $itemLayout = null;
    public self $listFieldComponent;

    public function __construct(
        public string $name,
        public ?string $label = null,
        public ?string $helpMessage = null,
        public ?bool $readOnly = null,
        public ?bool $addable = null,
        public ?bool $sortable = null,
        public ?bool $removable = null,
        public ?string $addText = null,
        public ?string $itemIdAttribute = null,
        public ?string $orderAttribute = null,
        public ?int $maxItemCount = null,
        public ?string $collapsedItemTemplatePath = null,
    ) {
        $this->field = SharpFormListField::make($this->name);
        $this->listField = $this->field;
        $this->listFieldComponent = $this;
    }
    
    protected function setItemLayout($itemLayout)
    {
        $this->itemLayout = $itemLayout;
    }
    
    protected function updateFromSlots(array $slots)
    {
        if ($template = $slots['collapsed_item'] ?? null) {
            $this->field->setCollapsedItemInlineTemplate($template);
        }
    }
}
