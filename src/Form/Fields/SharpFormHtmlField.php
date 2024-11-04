<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Formatters\HtmlFormatter;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Blade;

class SharpFormHtmlField extends SharpFormField
{
    const FIELD_TYPE = 'html';
    
    private View|string $template;
    
    public static function make(string $key): self
    {
        return new static($key, static::FIELD_TYPE, new HtmlFormatter);
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
