<?php

namespace Code16\Sharp\Utils\Fields;

interface IsSharpFieldWithLocalization
{
    public function setLocalized(bool $localized = true): self;

    public function isLocalized(): bool;
}
