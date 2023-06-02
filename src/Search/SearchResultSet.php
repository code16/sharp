<?php

namespace Code16\Sharp\Search;

use Code16\Sharp\Utils\Links\SharpLinkTo;

class SearchResultSet
{
    protected array $resultLinks = [];

    public function __construct(protected string $label, protected ?string $icon = null)
    {
    }

    public function addResultLink(SharpLinkTo $link, string $label, ?string $detail = null): ResultLink
    {
        return tap(new ResultLink($link, $label, $detail), function (ResultLink $resultLink) {
            $this->resultLinks[] = $resultLink;
        });
    }

    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'icon' => $this->icon,
            'results' => collect($this->resultLinks)
                ->map(fn (ResultLink $resultLink) => $resultLink->toArray())
                ->all(),
        ];
    }
}
