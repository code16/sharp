<?php

namespace Code16\Sharp\Utils\Links;

class LinkToDashboard extends SharpLinkTo
{
    public static function make(string $entityKey): self
    {
        return new static($entityKey);
    }

    public function renderAsUrl(): string
    {
        return route('code16.sharp.dashboard', $this->entityKey);
    }
}
