<?php

namespace Code16\Sharp\Form\Fields\Utils;

trait WithPlaceholder
{
    /**
     * @var string
     */
    protected $placeholder;

    /**
     * @param string $placeholder
     * @return static
     */
    public function setPlaceholder(string $placeholder)
    {
        $this->placeholder = $placeholder;

        return $this;
    }
}