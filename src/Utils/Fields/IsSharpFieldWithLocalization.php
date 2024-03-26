<?php

namespace Code16\Sharp\Utils\Fields;

interface IsSharpFieldWithLocalization
{
    function setLocalized(bool $localized = true): self;
    function isLocalized(): bool;
}