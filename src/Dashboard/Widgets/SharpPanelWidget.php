<?php

namespace Code16\Sharp\Dashboard\Widgets;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Blade;

class SharpPanelWidget extends SharpWidget
{
    private View|string $template;

    public static function make(string $key): self
    {
        return new static($key, 'panel');
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
