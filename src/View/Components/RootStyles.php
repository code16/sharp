<?php

namespace Code16\Sharp\View\Components;


use Code16\Sharp\View\Components\Utils\Colors;
use Illuminate\View\Component;

class RootStyles extends Component
{
    
    public string $primaryColor;
    
    public array $primaryColorHSL;
    
    public function __construct()
    {
        $this->primaryColor = config('sharp.theme.primary_color', '#004c9b');
        $this->primaryColorHSL = Colors::hexToHsl($this->primaryColor);
        $this->primaryColorLuminosity = Colors::luminosity($this->primaryColor);
    }
    
    public function render()
    {
        return view('sharp::components.root-styles', [
            'component' => $this,
        ]);
    }
}
