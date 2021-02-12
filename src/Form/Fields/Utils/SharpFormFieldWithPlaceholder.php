<?php

namespace Code16\Sharp\Form\Fields\Utils;

trait SharpFormFieldWithPlaceholder
{
    protected ?string $placeholder = null;

    public function setPlaceholder(string $placeholder): self
    {
        $this->placeholder = $placeholder;

        return $this;
    }
}