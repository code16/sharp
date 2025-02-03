<?php

namespace Code16\Sharp\EntityList\Fields;

interface IsEntityListField
{
    public function getFieldProperties(): array;

    public function setLabel(string $label): self;

    public function setSortable(bool $sortable = true): self;

    public function setWidth(int|string|float $width): self;

    public function setWidthFill(): self;

    public function hideOnSmallScreens(bool $hideOnSmallScreens = true): self;
}
