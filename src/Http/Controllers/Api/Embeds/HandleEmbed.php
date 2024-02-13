<?php

namespace Code16\Sharp\Http\Controllers\Api\Embeds;

use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Illuminate\Support\Str;

trait HandleEmbed
{
    protected function getEmbedFromKey(string $embedKey, ?array $payload = null): SharpFormEditorEmbed
    {
        $embed = app(Str::replace('.', '\\', $embedKey), ['payload' => $payload]);
        $embed->buildEmbedConfig();

        return $embed;
    }
}
