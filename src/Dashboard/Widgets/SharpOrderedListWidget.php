<?php

namespace Code16\Sharp\Dashboard\Widgets;

use Closure;
use Code16\Sharp\Utils\Links\SharpLinkTo;

class SharpOrderedListWidget extends SharpWidget
{
    /** @var Closure */
    protected $itemLinkBuilderClosure;

    public static function make(string $key): self
    {
        return new static($key, 'list');
    }

    public function buildItemLink(Closure $itemLinkBuilderClosure): self
    {
        $this->itemLinkBuilderClosure = $itemLinkBuilderClosure;

        return $this;
    }

    public function getItemUrl(array $item): ?string
    {
        if($closure = $this->itemLinkBuilderClosure) {
            if($link = $closure($item)) {
                return $link instanceof SharpLinkTo
                    ? $link->renderAsUrl()
                    : $link;
            }

            return null;
        }

        return null;
    }

    public function toArray(): array
    {
        return parent::buildArray([]);
    }
}