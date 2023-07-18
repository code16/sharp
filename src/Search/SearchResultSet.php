<?php

namespace Code16\Sharp\Search;

use Code16\Sharp\Utils\Links\SharpLinkTo;

class SearchResultSet
{
    protected array $resultLinks = [];
    protected ?string $emptyStateLabel = null;
    protected array $validationErrors = [];
    protected bool $hideWhenEmpty = false;

    public function __construct(protected string $label, protected ?string $icon = null)
    {
    }

    public final function addResultLink(SharpLinkTo $link, string $label, ?string $detail = null): ResultLink
    {
        return tap(new ResultLink($link, $label, $detail), function (ResultLink $resultLink) {
            $this->resultLinks[] = $resultLink;
        });
    }

    public final function hideWhenEmpty(bool $hideWhenEmpty = true): self
    {
        $this->hideWhenEmpty = $hideWhenEmpty;

        return $this;
    }

    public final function setEmptyStateLabel(string $emptyStateLabel): self
    {
        $this->emptyStateLabel = $emptyStateLabel;

        return $this;
    }

    public final function validateSearch(array $rules, array $messages = []): self
    {
        $validator = validator(request()->only('q'), ['q' => $rules], $messages);

        if ($validator->fails()) {
            $this->validationErrors = $validator->errors()->all();
        }

        return $this;
    }

    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'icon' => $this->icon,
            'showWhenEmpty' => !$this->hideWhenEmpty,
            'emptyStateLabel' => $this->emptyStateLabel,
            'validationErrors' => $this->validationErrors,
            'results' => collect($this->resultLinks)
                ->map(fn (ResultLink $resultLink) => $resultLink->toArray())
                ->all(),
        ];
    }
}
