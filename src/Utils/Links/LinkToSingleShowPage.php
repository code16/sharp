<?php

namespace Code16\Sharp\Utils\Links;

use Illuminate\Support\Collection;

class LinkToSingleShowPage extends SharpLinkTo
{
    public static function make(string $entityKey): self
    {
        return new static($entityKey);
    }

    public function renderAsUrl(): string
    {
        return route("code16.sharp.single-show", $this->entityKey);
    }
}