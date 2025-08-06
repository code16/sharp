<?php

namespace Code16\Sharp\Form\Fields\Utils;

trait SharpFormFieldWithHtmlSanitization
{
    protected bool $sanitize = false;

    public function shouldSanitizeHtml(bool $sanitize = true): self
    {
        $this->sanitize = $sanitize;

        return $this;
    }

    public function isSanitizingHtml(): bool
    {
        return $this->sanitize;
    }
}
