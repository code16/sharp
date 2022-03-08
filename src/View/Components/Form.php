<?php

namespace Code16\Sharp\View\Components;

use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Utils\Layout\LayoutColumn;
use Illuminate\View\Component;

class Form extends Component
{
    public function __construct(
        public SharpForm $form,
    ) {
    }

    public function render()
    {
        return view('sharp::components.form');
    }
}
