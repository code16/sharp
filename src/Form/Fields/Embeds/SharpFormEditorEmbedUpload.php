<?php

namespace Code16\Sharp\Form\Fields\Embeds;

use Code16\Sharp\Form\Fields\Formatters\UploadFormatter;
use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\Utils\IsUploadField;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithUpload;

class SharpFormEditorEmbedUpload extends SharpFormField implements IsUploadField
{
    use SharpFormFieldWithUpload;

    protected bool $hasLegend = false;

    public static function make(): self
    {
        return new static('none', 'upload', app(UploadFormatter::class));
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

    public function toArray(): array
    {
        return [];
    }
}
