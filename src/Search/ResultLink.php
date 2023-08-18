<?php

namespace Code16\Sharp\Search;

use Code16\Sharp\Utils\Links\SharpLinkTo;

class ResultLink
{
    public function __construct(
        protected SharpLinkTo $link,
        protected string $label,
        protected ?string $detail = null
    ) {
    }

    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'link' => $this->link->renderAsUrl(),
            'detail' => $this->detail,
        ];
    }
}
