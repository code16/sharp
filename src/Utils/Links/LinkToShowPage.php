<?php

namespace Code16\Sharp\Utils\Links;

class LinkToShowPage extends SharpLinkTo
{
    protected string $instanceId;

    public static function make(string $entityKey, string $instanceId): self
    {
        $instance = new static($entityKey);
        $instance->instanceId = $instanceId;

        return $instance;
    }

    public function renderAsUrl(): string
    {
        return route('code16.sharp.list.subpage', [
            'entityKey' => $this->entityKey,
            'uri'       => $this->generateUri(),
        ]);
    }

    protected function generateUri(): string
    {
        return sprintf('s-show/%s/%s', $this->entityKey, $this->instanceId);
    }
}
