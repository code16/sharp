<?php

namespace Code16\Sharp\Utils\Links;

class LinkToSingleForm extends LinkToSingleShowPage
{
    public function renderAsUrl(): string
    {
        return sprintf(
            '%s/s-form/%s',
            parent::renderAsUrl(),
            $this->entityKey,
        );
    }
}
