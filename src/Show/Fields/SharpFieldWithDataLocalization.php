<?php

namespace Code16\Sharp\Show\Fields;

trait SharpFieldWithDataLocalization
{
    protected ?bool $localized = null;

    public function setLocalized(bool $localized = true): self
    {
        $this->localized = $localized ?: null;

        return $this;
    }

    public function isLocalized(): bool
    {
        return $this->localized ?: false;
    }
}
