<?php

namespace Code16\Sharp\Show\Fields;

use Code16\Sharp\Show\Fields\Formatters\HtmlFormatter;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Blade;

class SharpShowHtmlField extends SharpShowField
{
    const FIELD_TYPE = 'html';
    
    protected ?string $label = null;
    private View|string $template;
    
    public static function make(string $key): SharpShowHtmlField
    {
        return new static($key, static::FIELD_TYPE, new HtmlFormatter());
    }
    
    public function setLabel(string $label): self
    {
        $this->label = $label;
        
        return $this;
    }
    
    public function toArray(): array
    {
        return parent::buildArray([]);
    }
    
    public function setTemplate(View|string $template): self
    {
        $this->template = $template;
        
        return $this;
    }
    
    public function render(array $data): string
    {
        if (is_string($this->template)) {
            return Blade::render($this->template, $data);
        }
        
        return $this->template->with($data)->render();
    }
}
