<?php

namespace Code16\Sharp\Utils\Links;

class LinkToDashboard extends SharpLinkTo
{
    public static function make(string $entityClassOrKey): self
    {
        return new static($entityClassOrKey);
    }

    public function renderAsUrl(): string
    {
        return route('code16.sharp.dashboard', [
            'globalFilter' => $this->globalFilter ?: sharp()->context()->globalFilterUrlSegmentValue(),
            'dashboardKey' => $this->entityKey,
        ]);
    }
}
