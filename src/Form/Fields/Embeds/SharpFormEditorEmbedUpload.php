<?php

namespace Code16\Sharp\Form\Fields\Embeds;

use Code16\Sharp\Form\Fields\Utils\IsUploadField;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithUpload;

class SharpFormEditorEmbedUpload implements IsUploadField
{
    use SharpFormFieldWithUpload;

    protected bool $hasLegend = false;

    private function __construct()
    {
    }

    public static function make(): self
    {
        return new self();
    }

    public function setHasLegend(bool $hasLegend = true): self
    {
        $this->hasLegend = $hasLegend;

        return $this;
    }

    public function hasLegend(): bool
    {
        return $this->hasLegend;
    }
}