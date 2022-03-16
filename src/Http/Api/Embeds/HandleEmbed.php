<?php

namespace Code16\Sharp\Http\Api\Embeds;

use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Illuminate\Support\Str;

trait HandleEmbed
{
    protected function getEmbedFromKey(string $embedKey): SharpFormEditorEmbed
    {
        $embed = app(Str::replace('.', '\\', $embedKey));
        $embed->buildEmbedConfig();
        
        return $embed;
    }
}