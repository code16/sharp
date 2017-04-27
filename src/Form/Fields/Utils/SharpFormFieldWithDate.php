<?php

namespace Code16\Sharp\Form\Fields\Utils;

use Carbon\Carbon;

trait SharpFormFieldWithDate
{
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
    protected $startDate;

    /**
     * @var string
     */
    protected $displayFormat = "yyyy-mm-dd";

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
     * @param Carbon $date
     * @return string
     */
    protected function formatDate(Carbon $date)
    {
        return $date->format("Y-m-d");
    }
}