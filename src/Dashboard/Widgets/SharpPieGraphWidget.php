<?php

namespace Code16\Sharp\Dashboard\Widgets;

class SharpPieGraphWidget extends SharpGraphWidget
{

    /**
     * @param string $key
     * @return static
     */
    public static function make(string $key)
    {
        $widget = new static($key, 'graph');
        $widget->display = 'pie';

        return $widget;
    }
}