<?php

namespace Code16\Sharp\Form\Fields;

use Closure;
use Code16\Sharp\Form\Fields\Formatters\HtmlFormatter;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Blade;

class SharpFormHtmlField extends SharpFormField
{
    const FIELD_TYPE = 'html';

    /** @var View|Closure(array, string)|string */
    private View|Closure|string $template;

    private bool $liveRefresh = false;
    private ?array $liveRefreshLinkedFields = null;

    public static function make(string $key): self
    {
        return new static($key, static::FIELD_TYPE, new HtmlFormatter());
    }

    public function setLiveRefresh(bool $liveRefresh = true, ?array $linkedFields = null): self
    {
        $this->liveRefresh = $liveRefresh;
        $this->liveRefreshLinkedFields = $linkedFields;

        return $this;
    }

    public function hasLiveRefresh(): bool
    {
        return $this->liveRefresh;
    }

    public function setTemplate(View|Closure|string $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function render(array $data, string $fieldKey): string
    {
        if ($this->template instanceof Closure) {
            $view = ($this->template)($data, $fieldKey);

            return $view instanceof View ? $view->render() : $view;
        }

        if (is_string($this->template)) {
            return Blade::render($this->template, $data);
        }

        return $this->template->with($data)->render();
    }

    public function toArray(): array
    {
        return parent::buildArray([
            'liveRefresh' => $this->liveRefresh,
            'liveRefreshLinkedFields' => $this->liveRefreshLinkedFields,
        ]);
    }
}
