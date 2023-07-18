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

    final public function addResultLink(SharpLinkTo $link, string $label, ?string $detail = null): ResultLink
    {
        return tap(new ResultLink($link, $label, $detail), function (ResultLink $resultLink) {
            $this->resultLinks[] = $resultLink;
        });
    }

    final public function hideWhenEmpty(bool $hideWhenEmpty = true): self
    {
        $this->hideWhenEmpty = $hideWhenEmpty;

        return $this;
    }

    final public function setEmptyStateLabel(string $emptyStateLabel): self
    {
        $this->emptyStateLabel = $emptyStateLabel;

        return $this;
    }

    final public function validateSearch(array $rules, array $messages = []): bool
    {
        $validator = validator(
            request()->only('q'), 
            ['q' => $rules], 
            $messages
        );

        if ($validator->fails()) {
            $this->validationErrors = $validator->errors()->all();
            
            return false;
        }

        return true;
    }

    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'icon' => $this->icon,
            'showWhenEmpty' => ! $this->hideWhenEmpty,
            'emptyStateLabel' => $this->emptyStateLabel,
            'validationErrors' => $this->validationErrors,
            'results' => empty($this->validationErrors) 
                ? collect($this->resultLinks)
                    ->map(fn (ResultLink $resultLink) => $resultLink->toArray())
                    ->all()
                : [],
        ];
    }
}
