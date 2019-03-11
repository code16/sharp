<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Formatters\NumberFormatter;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithPlaceholder;

class SharpFormNumberField extends SharpFormField
{
    use SharpFormFieldWithPlaceholder;

    const FIELD_TYPE = "number";

    /**
     * @var int
     */
    protected $min;

    /**
     * @var int
     */
    protected $max;

    /**
     * @var int
     */
    protected $step = 1;

    /**
     * @var bool
     */
    protected $showControls = false;

    /**
     * @param string $key
     * @return static
     */
    public static function make(string $key)
    {
        return new static($key, static::FIELD_TYPE, new NumberFormatter);
    }

    /**
     * @param int $min
     * @return $this
     */
    public function setMin(int $min)
    {
        $this->min = $min;

        return $this;
    }

    /**
     * @param int $max
     * @return $this
     */
    public function setMax(int $max)
    {
        $this->max = $max;

        return $this;
    }

    /**
     * @param int $step
     * @return $this
     */
    public function setStep(int $step)
    {
        $this->step = $step;

        return $this;
    }

    /**
     * @param bool $showControls
     * @return $this
     */
    public function setShowControls(bool $showControls = true)
    {
        $this->showControls = $showControls;

        return $this;
    }


    /**
     * @return array
     */
    protected function validationRules()
    {
        return [
            "min" => "integer",
            "max" => "integer",
            "step" => "required|integer",
            "showControls" => "required|bool",
        ];
    }

    /**
     * @return array
     * @throws \Code16\Sharp\Exceptions\Form\SharpFormFieldValidationException
     */
    public function toArray(): array
    {
        return parent::buildArray([
            "min" => $this->min,
            "max" => $this->max,
            "step" => $this->step,
            "showControls" => $this->showControls,
            "placeholder" => $this->placeholder,
        ]);
    }
}