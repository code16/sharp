<?php

namespace Code16\Sharp\Dashboard\Widgets;

class SharpFigureWidget extends SharpWidget
{
    public static function make(string $key): self
    {
        return new static($key, 'figure');
    }

    public function toArray(): array
    {
        return parent::buildArray([]);
    }
}
