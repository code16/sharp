<?php

namespace Code16\Sharp\Form\Fields\Utils;

use Code16\Sharp\Form\Fields\Editor\Uploads\SharpFormEditorUpload;
use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Illuminate\Support\Collection;

trait SharpFormFieldWithEmbeds
{
    protected array $embeds = [];
    protected ?SharpFormEditorUpload $uploadsConfig = null;

    public function allowEmbeds(array $embeds): self
    {
        $this->embeds = $embeds;

        return $this;
    }
    
    /**
     * @return Collection<int, SharpFormEditorEmbed>
     */
    public function embeds(): Collection
    {
        return once(fn () => collect($this->embeds)
            ->map(fn (string $embedClass) => app($embedClass))
            ->each(function(SharpFormEditorEmbed $embed) {
                $embed->buildEmbedConfig();
                return $embed;
            }));
    }

    protected function innerComponentEmbedsConfiguration(bool $isForm = true): ?array
    {
        if (empty($this->embeds)) {
            return null;
        }

        return $this->embeds()
            ->mapWithKeys(function (SharpFormEditorEmbed $embed) use ($isForm) {
                $embed->buildEmbedConfig();

                return [
                    $embed->key() => $embed->toConfigArray($isForm),
                ];
            })
            ->toArray();
    }
}
