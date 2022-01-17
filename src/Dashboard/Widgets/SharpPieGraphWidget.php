<?php

namespace Code16\Sharp\Dashboard\Widgets;

class SharpPieGraphWidget extends SharpGraphWidget
{
    public static function make(string $key): self
    {
        $widget = new static($key, 'graph');
        $widget->display = 'pie';

        return $widget;
    }
}
