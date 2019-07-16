<?php

namespace Code16\Sharp\Dashboard\Widgets;

class SharpListWidget extends SharpWidget
{

    /**
     * @var bool
     */
    protected $withCounts;

    /**
     * @param string $key
     * @return static
     */
    public static function make(string $key)
    {
        $widget = new static($key, 'list');
        $widget->withCounts = false;

        return $widget;
    }

    /**
     * @param bool $withCounts
     * @return $this
     */
    public function setWithCounts(bool $withCounts = true)
    {
        $this->withCounts = $withCounts;

        return $this;
    }

    /**
     * @return array
     * @throws \Code16\Sharp\Exceptions\Dashboard\SharpWidgetValidationException
     */
    public function toArray(): array
    {
        return parent::buildArray([
            "withCounts" => $this->withCounts
        ]);
    }

}