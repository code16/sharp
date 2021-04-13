<?php

namespace Code16\Sharp\Form\Fields\Utils;

trait SharpFormFieldWithMaxLength
{
    protected ?int $maxLength = null;

    public function setMaxLength(int $maxLength): self
    {
        $this->maxLength = $maxLength;

        return $this;
    }

    public function setMaxLengthUnlimited(): self
    {
        $this->maxLength = null;

        return $this;
    }
}