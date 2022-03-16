<?php

namespace Code16\Sharp\Http\Api\Embeds;

use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class EmbedsController extends Controller
{
    /**
     * Return formatted data to display an embed (in an Editor field or in a Show field)
     */
    public function show(string $embedKey, string $entityKey, ?string $instanceId = null)
    {
        sharp_check_ability('view', $entityKey, $instanceId);

        /** @var SharpFormEditorEmbed $embed */
        $embed = app(Str::replace('.', '\\', $embedKey));
        
        return response()->json([
            'embeds' => collect(request()->get('embeds'))
                ->map(fn (array $attributes) => $embed->fillTemplateWith($attributes, request()->boolean('form'))),
        ]);
    }
}
