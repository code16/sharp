<?php

namespace Code16\Sharp\View\Components;

use Code16\Sharp\View\Components\Utils\MenuItem;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Title extends Component
{
    public ?string $label;
    
    public function __construct()
    {
        $this->label = $this->currentEntityLabel();
    }
    
    public function currentEntityLabel(): ?string
    {
        // todo
        return null;
    }

    public function render()
    {
        return view('sharp::components.title', [
            'component' => $this,
        ]);
    }
}
