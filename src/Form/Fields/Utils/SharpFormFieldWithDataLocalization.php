<?php

namespace Code16\Sharp\Form\Fields\Utils;

trait SharpFormFieldWithDataLocalization
{
    /**
     * @var bool
     */
    protected $localized = null;

    /**
     * @param bool $localized
     * @return static
     */
    public function setLocalized(bool $localized = true)
    {
        $this->localized = $localized ?: null;

        return $this;
    }
}