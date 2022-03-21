<?php

namespace Code16\Sharp\Form\Fields\Utils;

use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;

trait SharpFormFieldWithEmbeds
{
    protected array $embeds = [];

    public function allowEmbeds(array $embeds): self
    {
        $this->embeds = $embeds;

        return $this;
    }

    protected function innerComponentEmbedsConfiguration(bool $isForm = true): array
    {
        return collect($this->embeds)
            ->map(fn (string $embedClass) => app($embedClass))
            ->each->buildEmbedConfig()
            ->mapWithKeys(function (SharpFormEditorEmbed $embed) use ($isForm) {
                return [
                    $embed->key() => $embed->toConfigArray($isForm)
                ];
            })
            ->toArray();
    }
}
