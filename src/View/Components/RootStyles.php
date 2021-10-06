<?php

namespace Code16\Sharp\View\Components;


use Code16\Sharp\View\Components\Utils\Colors;
use Illuminate\View\Component;
use \NumberFormatter;

class RootStyles extends Component
{
    
    public string $primaryColor;
    public string $primaryColorLuminosity;
    public array $primaryColorHSL;
    
    public function __construct()
    {
        $this->primaryColor = config('sharp.theme.primary_color', '#004c9b');
        $this->primaryColorHSL = Colors::hexToHsl($this->primaryColor);
        $this->primaryColorLuminosity = Colors::luminosity($this->primaryColor);
    }
    
    public function formatNumber($num): string
    {
        $formatter = new NumberFormatter('en', NumberFormatter::DECIMAL);
        $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 5);
        $formatter->setSymbol(NumberFormatter::DECIMAL_SEPARATOR_SYMBOL, '.');
        $formatter->setSymbol(NumberFormatter::GROUPING_SEPARATOR_SYMBOL, '');
        return $formatter->format($num);
    }
    
    public function render()
    {
        return view('sharp::components.root-styles', [
            'self' => $this,
        ]);
    }
}
