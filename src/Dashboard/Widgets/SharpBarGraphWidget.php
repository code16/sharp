<?php

namespace Code16\Sharp\Dashboard\Widgets;

class SharpBarGraphWidget extends SharpGraphWidget
{

    /**
     * @param string $key
     * @return static
     */
    public static function make(string $key)
    {
        $widget = new static($key, 'graph');
        $widget->display = 'bar';

        return $widget;
    }
}