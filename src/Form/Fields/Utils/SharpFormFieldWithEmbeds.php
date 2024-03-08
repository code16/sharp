<?php

namespace Code16\Sharp\Form\Fields\Utils;

use Code16\Sharp\Form\Fields\Editor\Uploads\SharpFormEditorUpload;
use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;

trait SharpFormFieldWithEmbeds
{
    protected array $embeds = [];
    protected ?SharpFormEditorUpload $uploadsConfig = null;

    public function allowEmbeds(array $embeds): self
    {
        $this->embeds = $embeds;

        return $this;
    }

    protected function innerComponentEmbedsConfiguration(bool $isForm = true): ?array
    {
        if (empty($this->embeds)) {
            return null;
        }

        return collect($this->embeds)
            ->map(fn (string $embedClass) => app($embedClass))
            ->mapWithKeys(function (SharpFormEditorEmbed $embed) use ($isForm) {
                $embed->buildEmbedConfig();

                return [
                    $embed->key() => $embed->toConfigArray($isForm),
                ];
            })
            ->toArray();
    }
}
