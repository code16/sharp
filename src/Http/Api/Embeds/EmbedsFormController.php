<?php

namespace Code16\Sharp\Http\Api\Embeds;

use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class EmbedsFormController extends Controller
{
    public function show(string $embedKey, string $entityKey, ?string $instanceId = null)
    {
        sharp_check_ability('update', $entityKey, $instanceId);

        /** @var SharpFormEditorEmbed $embed */
        $embed = app(Str::replace('.', '\\', $embedKey));
        $embed->buildEmbedConfig();
        
        return [
            'fields' => $embed->fields(),
            'layout' => $embed->formLayout(),
            'config' => $embed->formConfig(),
            'data' => $embed->fillFormWith(request()->all()),
        ];
    }

    public function update(string $embedKey, string $entityKey, ?string $instanceId = null)
    {
        sharp_check_ability('update', $entityKey, $instanceId);

        /** @var SharpFormEditorEmbed $embed */
        $embed = app(Str::replace('.', '\\', $embedKey));
        $embed->buildEmbedConfig();
        
        $data = $embed->updateContent(
            $embed->formatRequestData(request()->all())
        );
        
        return $embed->fillTemplateWith($data, true);
    }
}
