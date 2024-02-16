<?php

namespace Code16\Sharp\Form\Fields\Editor\Uploads;

use Code16\Sharp\Form\Fields\Formatters\UploadFormatter;
use Code16\Sharp\Form\Fields\SharpFormUploadField;

class SharpFormEditorUpload extends SharpFormUploadField
{
    protected bool $hasLegend = false;

    public static function make(string $key): self
    {
        return new static($key, 'upload', app(UploadFormatter::class));
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
