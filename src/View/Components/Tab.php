<?php

namespace Code16\Sharp\View\Components;

use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\Layout\FormLayoutTab;
use Code16\Sharp\Utils\Layout\LayoutColumn;
use Illuminate\View\Component;

class Tab extends Component
{
    public FormLayoutTab $tab;
    public LayoutColumn $currentColumn;
    public self $tabComponent;

    public function __construct(
        public ?string $title = null,
    ) {
        $this->tab = new FormLayoutTab($title);
        $this->currentColumn = new FormLayoutColumn(12);
        $this->tabComponent = $this;
    }

    public function addColumn(int $size)
    {
        $this->currentColumn->setSize($size);
        $this->tab->addColumnLayout($this->currentColumn);
        $this->currentColumn = new FormLayoutColumn(12);
    }

    public function render()
    {
        return view('sharp::components.form');
    }
}
