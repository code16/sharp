<?php

namespace Code16\Sharp\Utils\Fields;

use Code16\Sharp\Form\Fields\Editor\Uploads\SharpFormEditorUpload;
use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Illuminate\Support\Collection;

trait SharpFieldWithEmbeds
{
    protected array $embeds = [];
    protected ?SharpFormEditorUpload $uploadsConfig = null;

    public function allowEmbeds(array $embeds): self
    {
        $this->embeds = $embeds;

        return $this;
    }

    /**
     * @return Collection<string, SharpFormEditorEmbed>
     */
    public function embeds(): Collection
    {
        return once(fn () => collect($this->embeds)
            ->map(fn (string $embedClass) => app($embedClass))
            ->mapWithKeys(function (SharpFormEditorEmbed $embed) {
                $embed->buildEmbedConfig();

                return [
                    $embed->key() => $embed,
                ];
            }));
    }
    
    protected function getAllowedEmbed(string $embedClass): ?SharpFormEditorEmbed
    {
        return $this->embeds()->first(fn (SharpFormEditorEmbed $embed) => $embed instanceof $embedClass);
    }

    protected function innerComponentEmbedsConfiguration(bool $isForm = true): ?array
    {
        if (empty($this->embeds)) {
            return null;
        }

        return $this->embeds()
            ->map(fn (SharpFormEditorEmbed $embed) => $embed->toConfigArray($isForm))
            ->toArray();
    }
}
