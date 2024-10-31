<?php

namespace Code16\Sharp\Form\Fields\Utils;

interface IsSharpFormAutocompleteField
{
    public function setItemIdAttribute(string $itemIdAttribute): self;
    public function setAdditionalTemplateData(array $data): self;
    public function isRemote(): bool;
    public function isLocal(): bool;
    public function itemIdAttribute(): string;
}
