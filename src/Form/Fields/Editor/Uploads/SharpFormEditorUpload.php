<?php

namespace Code16\Sharp\Form\Fields\Editor\Uploads;

use Code16\Sharp\Exceptions\SharpInvalidConfigException;
use Code16\Sharp\Form\Fields\Formatters\UploadFormatter;
use Code16\Sharp\Form\Fields\SharpFormUploadField;

class SharpFormEditorUpload extends SharpFormUploadField
{
    protected bool $hasLegend = false;

    public static function make(?string $key = 'file'): self
    {
        return new static($key, 'upload', app(UploadFormatter::class));
    }
    
    /**
     * @throws SharpInvalidConfigException
     */
    public function setStorageTemporary(): SharpFormUploadField
    {
        throw new SharpInvalidConfigException('Temporary storage is not available for editor uploads');
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
