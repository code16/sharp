<?php

namespace Code16\Sharp\View\Components;

use Illuminate\View\Component;

class Field extends Component
{
    public string $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function render()
    {
        $component = "x-sharp::form.fields.$this->type";
        if(!view()->exists($component)) {
            return null;
        }
        return "<$component />";
    }
}
