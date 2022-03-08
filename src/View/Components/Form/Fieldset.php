<?php

namespace Code16\Sharp\View\Components\Form;

use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\Layout\FormLayoutFieldset;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Utils\Layout\LayoutColumn;
use Code16\Sharp\View\Components\Col;
use Illuminate\View\Component;

class Fieldset extends Component
{
    public FormLayoutFieldset $fieldset;
    protected ?Col $parentColComponent = null;
    
    public function __construct(
        public ?string $legend = null,
    ) {
        $this->parentColComponent = view()->getConsumableComponentData('colComponent');
        $this->parentColComponent->column->withFieldset($this->legend ?? '', function (FormLayoutFieldset $fieldset) {
            $this->fieldset = $fieldset;
        });
    }

    public function render(): callable
    {
        return function ($data) {
            $this->fieldset->setLegend($data['legend']);
        };
    }
}
