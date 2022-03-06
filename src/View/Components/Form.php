<?php

namespace Code16\Sharp\View\Components;

use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Utils\Layout\LayoutColumn;
use Illuminate\View\Component;

class Form extends Component
{
    public LayoutColumn $currentColumn;
    public self $formComponent;

    public function __construct(
        public SharpForm $form,
    ) {
        $this->currentColumn = new FormLayoutColumn(12);
        $this->formComponent = $this;
    }

    public function addColumn(int $size)
    {
        $this->currentColumn->setSize($size);
        $this->form
            ->formLayoutInstance()
            ->addColumnLayout($this->currentColumn);
        $this->currentColumn = new FormLayoutColumn(12);
    }

    public function render()
    {
        return view('sharp::components.form');
    }
}
