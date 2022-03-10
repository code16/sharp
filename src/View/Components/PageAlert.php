<?php

namespace Code16\Sharp\View\Components;

use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Utils\Traits\HandlePageAlertMessage;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class PageAlert extends Component
{
    /**
     * @var HandlePageAlertMessage
     */
    protected $page;
    
    public function __construct(
        public ?string $level = null,
        ?string $class = null
    ) {
        $this->page = view()->getConsumableComponentData('form');
        
        if ($class) {
            $this->level = Str::match("/alert-(.+)/", $class);
        }
    }

    public function render(): callable
    {
        return function ($data) {
            $this->page->configurePageAlert(
                $data['slot'],
                $this->level,
            );
        };
    }
}
