<?php

namespace Code16\Sharp\View\Components;

use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\Layout\FormLayoutTab;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Utils\Layout\LayoutColumn;
use Illuminate\View\Component;

class Tab extends Component
{
    public FormLayoutTab $tab;
    public SharpForm $form;

    public function __construct(
        public ?string $title = null,
    ) {
        $this->form = view()->getConsumableComponentData('form');
        $this->form
            ->formLayoutInstance()
            ->addTab($this->title ?? '', function (FormLayoutTab $tab) {
                $this->tab = $tab;
            });
    }

    public function render(): callable
    {
        return function ($data) {
            $this->tab->setTitle($data['title']);
        };
    }
}
