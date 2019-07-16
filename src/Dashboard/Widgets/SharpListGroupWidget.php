<?php

namespace Code16\Sharp\Dashboard\Widgets;

class SharpListGroupWidget extends SharpWidget
{

    /**
     * @param string $key
     * @return static
     */
    public static function make(string $key)
    {
        $widget = new static($key, 'list');

        return $widget;
    }

    /**
     * @return array
     * @throws \Code16\Sharp\Exceptions\Dashboard\SharpWidgetValidationException
     */
    public function toArray(): array
    {
        return parent::buildArray([]);
    }

}