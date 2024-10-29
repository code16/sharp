<?php

namespace Code16\Sharp\Dashboard\Widgets;

use Illuminate\Contracts\View\View;

class SharpPanelWidget extends SharpWidget
{
    private View $template;

    public static function make(string $key): self
    {
        return new static($key, 'panel');
    }

    public function toArray(): array
    {
        return parent::buildArray([]);
    }

    public function setTemplate(View $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function render(array $data): string
    {
        return $this->template->with($data)->render();
    }
}
