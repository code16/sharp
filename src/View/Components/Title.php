<?php

namespace Code16\Sharp\View\Components;

use Illuminate\View\Component;

class Title extends Component
{
    public function currentEntityLabel(): ?string
    {
        return currentSharpRequest()->getCurrentEntityMenuLabel();
    }

    public function render()
    {
        return view('sharp::components.title', [
            'component' => $this,
        ]);
    }
}
