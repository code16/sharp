<?php

namespace Code16\Sharp\Utils\Fields;

interface LocalizedSharpField
{
    function setLocalized(bool $localized = true): self;
    function isLocalized(): bool;
}