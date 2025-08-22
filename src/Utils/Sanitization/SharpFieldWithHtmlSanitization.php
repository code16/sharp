<?php

namespace Code16\Sharp\Utils\Sanitization;

trait SharpFieldWithHtmlSanitization
{
    protected bool $sanitizeHtml = false;

    public function setSanitizeHtml(bool $sanitizeHtml = true): self
    {
        $this->sanitizeHtml = $sanitizeHtml;

        return $this;
    }

    /**
     * @internal
     */
    public function isSanitizingHtml(): bool
    {
        return $this->sanitizeHtml;
    }
}
