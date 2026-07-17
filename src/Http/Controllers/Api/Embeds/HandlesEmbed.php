<?php

namespace Code16\Sharp\Http\Controllers\Api\Embeds;

use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Illuminate\Support\Str;

trait HandlesEmbed
{
    protected function getEmbedFromKey(string $embedKey): SharpFormEditorEmbed
    {
        $embedClass = Str::replace('.', '\\', $embedKey);

        if (! is_a($embedClass, SharpFormEditorEmbed::class, true)) {
            throw new \Exception("Embed class $embedClass is not a SharpFormEditorEmbed");
        }

        $embed = app($embedClass);
        $embed->buildEmbedConfig();

        return $embed;
    }
}
