<?php

namespace Code16\Sharp\Form\Fields;

use Carbon\Carbon;

class SharpFormDateField extends SharpFormField
{
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
    protected $minDate;

    /**
     * @var string
     */
    protected $maxDate;

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
     * @var string
     */
    protected $startDate;

    /**
     * @var string
     */
    protected $displayFormat = "yyyy-mm-dd";


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
     * @param Carbon $minDate
     * @return $this
     */
    public function setMinDate(Carbon $minDate)
    {
        $this->minDate = $this->formatDate($minDate);

        return $this;
    }

    /**
     * @param Carbon $maxDate
     * @return $this
     */
    public function setMaxDate(Carbon $maxDate)
    {
        $this->maxDate = $this->formatDate($maxDate);

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
     * @param Carbon $startDate
     * @return $this
     */
    public function setStartDate(Carbon $startDate)
    {
        $this->startDate = $this->formatDate($startDate);

        return $this;
    }

    /**
     * @param string $displayFormat
     * @return $this
     */
    public function setDisplayFormat(string $displayFormat)
    {
        $this->displayFormat = $displayFormat;

        return $this;
    }


    /**
     * @return array
     */
    public function toArray(): array
    {
        return parent::makeArray([
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
     * @param Carbon $date
     * @return string
     */
    private function formatDate(Carbon $date)
    {
        return $date->format("Y-m-d");
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