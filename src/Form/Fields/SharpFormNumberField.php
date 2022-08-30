<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Formatters\NumberFormatter;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithPlaceholder;

class SharpFormNumberField extends SharpFormField
{
    use SharpFormFieldWithPlaceholder;

    const FIELD_TYPE = 'number';

    protected ?float $min = null;
    protected ?float $max = null;
    protected float $step = 1;
    protected bool $showControls = false;

    public static function make(string $key): self
    {
        return new static($key, static::FIELD_TYPE, new NumberFormatter);
    }

    public function setMin(float $min): self
    {
        $this->min = $min;

        return $this;
    }

    public function setMax(float $max): self
    {
        $this->max = $max;

        return $this;
    }

    public function setStep(float $step): self
    {
        $this->step = $step;

        return $this;
    }

    public function setShowControls(bool $showControls = true): self
    {
        $this->showControls = $showControls;

        return $this;
    }

    protected function validationRules(): array
    {
        return [
            'min' => 'numeric',
            'max' => 'numeric',
            'step' => 'required|numeric',
            'showControls' => 'required|bool',
        ];
    }

    public function toArray(): array
    {
        return parent::buildArray([
            'min' => $this->min,
            'max' => $this->max,
            'step' => $this->step,
            'showControls' => $this->showControls,
            'placeholder' => $this->placeholder,
        ]);
    }
}
