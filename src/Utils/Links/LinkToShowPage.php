<?php

namespace Code16\Sharp\Utils\Links;

use Illuminate\Support\Collection;

class LinkToShowPage extends SharpLinkTo
{
    /** @var string */
    protected $instanceId;

    public static function createFor(string $entityKey, string $instanceId): self
    {
        $instance = new static($entityKey);
        $instance->instanceId = $instanceId;
        
        return $instance;
    }

    public function renderAsUrl(): string
    {
        return route("code16.sharp.list.subpage", [
            "entityKey" => $this->entityKey,
            "uri" => $this->generateUri()
        ]);
    }

    protected function generateUri(): string
    {
        return sprintf("s-show/%s/%s", $this->entityKey, $this->instanceId);
    }
}