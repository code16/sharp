<?php

namespace Code16\Sharp\Dashboard\Widgets;

use Illuminate\Validation\Rule;

class SharpFigureWidget extends SharpWidget
{
    protected ?string $figure = null;
    protected ?string $unit = null;
    protected string $variant = 'primary';
    protected string $size = 'M';

    public static function make(string $key): self
    {
        return new static($key, 'figure');
    }

    public function toArray(): array
    {
        return parent::buildArray([
            'figure' => $this->figure,
            'unit' => $this->unit,
            'variant' => $this->variant,
            'size' => $this->size,
        ]);
    }

    public function setVariantPrimary(): self
    {
        $this->variant = 'primary';

        return $this;
    }

    public function setVariantSecondary(): self
    {
        $this->variant = 'secondary';

        return $this;
    }

    public function setVariantWarning(): self
    {
        $this->variant = 'warning';

        return $this;
    }

    public function setVariantDanger(): self
    {
        $this->variant = 'danger';

        return $this;
    }

    public function setSizeMedium(): self
    {
        $this->size = 'M';
        
        return $this;
    }

    public function setSizeSmall(): self
    {
        $this->size = 'S';

        return $this;
    }

    public function setSizeLarge(): self
    {
        $this->size = 'L';

        return $this;
    }

    protected function validationRules(): array
    {
        return [
            'variant' => ['required', Rule::in(['primary', 'secondary', 'warning', 'danger'])],
            'size' => ['required', Rule::in(['S', 'M', 'L'])],
        ];
    }
}
