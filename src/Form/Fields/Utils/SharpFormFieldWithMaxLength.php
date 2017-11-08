<?php

namespace Code16\Sharp\Form\Fields\Utils;

trait SharpFormFieldWithMaxLength
{
    /**
     * @var int
     */
    protected $maxLength = 0;

    /**
     * @param int $maxLength
     * @return static
     */
    public function setMaxLength(int $maxLength)
    {
        $this->maxLength = $maxLength;

        return $this;
    }

    /**
     * @return static
     */
    public function setMaxLengthUnlimited()
    {
        $this->maxLength = 0;

        return $this;
    }
}