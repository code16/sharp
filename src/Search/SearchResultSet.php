<?php

namespace Code16\Sharp\Search;

use Code16\Sharp\Utils\Links\SharpLinkTo;

class SearchResultSet
{
    protected array $resultLinks = [];
    protected ?string $emptyStateLabel = null;

    public function __construct(protected string $label, protected ?string $icon = null)
    {
    }

    public final function addResultLink(SharpLinkTo $link, string $label, ?string $detail = null): ResultLink
    {
        return tap(new ResultLink($link, $label, $detail), function (ResultLink $resultLink) {
            $this->resultLinks[] = $resultLink;
        });
    }

    public final function setEmptyStateLabel(string $emptyStateLabel): self
    {
        $this->emptyStateLabel = $emptyStateLabel;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'icon' => $this->icon,
            'emptyStateLabel' => $this->emptyStateLabel ?: trans('sharp::entity_list.empty_text'),
            'results' => collect($this->resultLinks)
                ->map(fn (ResultLink $resultLink) => $resultLink->toArray())
                ->all(),
        ];
    }
}
