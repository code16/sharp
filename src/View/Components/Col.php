<?php

namespace Code16\Sharp\View\Components;

use Code16\Sharp\Utils\Layout\LayoutColumn;
use Code16\Sharp\View\Utils\InjectComponentData;
use Illuminate\View\Component;

class Col extends Component
{
    use InjectComponentData;

    public self $colComponent;
    public ?self $parentColComponent = null;
    protected ?Form $formComponent;
    protected ?Tab $tabComponent;

    public function __construct(
        public ?int $size = null,
        public ?int $sizeXs = null,
    ) {
        $this->colComponent = $this;
        $this->parentColComponent = $this->aware('colComponent');
        $this->formComponent = $this->aware('formComponent');
        $this->tabComponent = $this->aware('tabComponent');
    }

    protected function layoutColumn(): LayoutColumn
    {
        return $this->tabComponent->currentColumn
            ?? $this->formComponent->currentColumn;
    }

    public function addField($name)
    {
        $key = "$name|$this->size";
        if ($this->parentColComponent) {
            $this->layoutColumn()->appendLastRowField($key);
        } else {
            $this->layoutColumn()->withSingleField($name);
        }
    }

    public function render(): callable
    {
        return function () {
            if ($this->parentColComponent) {
                return;
            }
            if ($this->tabComponent) {
                $this->tabComponent->addColumn($this->size);
            } elseif ($this->formComponent) {
                $this->formComponent->addColumn($this->size);
            }
        };
    }
}
