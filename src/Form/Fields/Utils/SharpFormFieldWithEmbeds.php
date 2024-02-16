<?php

namespace Code16\Sharp\Form\Fields\Utils;

use Closure;
use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbedUpload;

trait SharpFormFieldWithEmbeds
{
    protected array $embeds = [];
    protected ?SharpFormEditorEmbedUpload $embedUploadsConfig = null;

    public function allowEmbeds(array $embeds): self
    {
        $this->embeds = $embeds;

        return $this;
    }

    public function allowUploads(Closure $callback): self
    {
        $this->embedUploadsConfig = SharpFormEditorEmbedUpload::make();
        $callback($this->embedUploadsConfig);

        return $this;
    }

    public function embedUploadsConfig(): ?SharpFormEditorEmbedUpload
    {
        return $this->embedUploadsConfig;
    }

    protected function innerComponentEmbedsConfiguration(bool $isForm = true): array
    {
        return collect($this->embeds)
            ->map(fn (string $embedClass) => app($embedClass))
            ->each->buildEmbedConfig()
            ->mapWithKeys(function (SharpFormEditorEmbed $embed) use ($isForm) {
                return [
                    $embed->key() => $embed->toConfigArray($isForm),
                ];
            })
            ->toArray();
    }
}
