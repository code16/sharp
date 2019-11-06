<?php

namespace Code16\Sharp\Dashboard\Widgets;

use Closure;
use Code16\Sharp\Utils\LinkToEntity;

class SharpOrderedListWidget extends SharpWidget
{
    /** @var Closure */
    protected $itemLinkBuilderClosure;

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
     * @param Closure $itemLinkBuilderClosure
     * @return $this
     */
    public function buildItemLink(Closure $itemLinkBuilderClosure)
    {
        $this->itemLinkBuilderClosure = $itemLinkBuilderClosure;

        return $this;
    }

    /**
     * @param array $item
     * @return string
     */
    public function getItemUrl(array $item)
    {
        if($closure = $this->itemLinkBuilderClosure) {
            if($link = $closure(new LinkToEntity(), $item)) {
                return $link->renderAsUrl();
            }

            return null;
        }

        return null;
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