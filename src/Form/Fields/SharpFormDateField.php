<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithDate;

class SharpFormDateField extends SharpFormField
{
    use SharpFormFieldWithDate;

    /**
     * @var bool
     */
    protected $hasDate = true;

    /**
     * @var bool
     */
    protected $hasTime = false;

    /**
     * @var string
     */
    protected $minTime;

    /**
     * @var string
     */
    protected $maxTime;

    /**
     * @var int
     */
    protected $stepTime = 30;

    /**
     * @param string $key
     * @return static
     */
    public static function make(string $key)
    {
        $instance = new static($key, 'date');
        $instance->startDate = date("Y-m-d");

        return $instance;
    }

    /**
     * @param bool $hasDate
     * @return $this
     */
    public function setHasDate($hasDate = true)
    {
        $this->hasDate = $hasDate;

        return $this;
    }

    /**
     * @param bool $hasTime
     * @return $this
     */
    public function setHasTime($hasTime = true)
    {
        $this->hasTime = $hasTime;

        return $this;
    }

    /**
     * @param int $hours
     * @param int $minutes
     * @return $this
     */
    public function setMinTime(int $hours, int $minutes = 0)
    {
        $this->minTime = $this->formatTime($hours, $minutes);

        return $this;
    }

    /**
     * @param int $hours
     * @param int $minutes
     * @return $this
     */
    public function setMaxTime(int $hours, int $minutes = 0)
    {
        $this->maxTime = $this->formatTime($hours, $minutes);

        return $this;
    }

    /**
     * @param int $step
     * @return $this
     */
    public function setStepTime(int $step)
    {
        $this->stepTime = $step;

        return $this;
    }

    /**
     * @return array
     */
    protected function validationRules()
    {
        return [
            "hasDate" => "required|boolean",
            "hasTime" => "required|boolean",
            "minDate" => "date_format:Y-m-d",
            "startDate" => "required|date_format:Y-m-d",
            "displayFormat" => "required",
            "maxDate" => "date_format:Y-m-d",
            "minTime" => "regex:/[0-9]{2}:[0-9]{2}/",
            "maxTime" => "regex:/[0-9]{2}:[0-9]{2}/",
            "stepTime" => "integer|min:1|max:60",
        ];
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return parent::buildArray([
            "hasDate" => $this->hasDate,
            "hasTime" => $this->hasTime,
            "startDate" => $this->startDate,
            "minDate" => $this->minDate,
            "maxDate" => $this->maxDate,
            "minTime" => $this->minTime,
            "maxTime" => $this->maxTime,
            "stepTime" => $this->stepTime,
            "displayFormat" => $this->displayFormat,
        ]);
    }

    /**
     * @param int $hours
     * @param int $minutes
     * @return string
     */
    private function formatTime(int $hours, int $minutes)
    {
        return str_pad($hours, 2, "0", STR_PAD_LEFT)
            . ":"
            . str_pad($minutes, 2, "0", STR_PAD_LEFT);
    }
}