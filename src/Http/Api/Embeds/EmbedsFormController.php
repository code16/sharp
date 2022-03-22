<?php

namespace Code16\Sharp\Http\Api\Embeds;

use Illuminate\Routing\Controller;

class EmbedsFormController extends Controller
{
    use HandleEmbed;

    public function show(string $embedKey, string $entityKey, ?string $instanceId = null)
    {
        if ($instanceId) {
            sharp_check_ability('view', $entityKey, $instanceId);
        } else {
            sharp_check_ability('entity', $entityKey);
        }

        $embed = $this->getEmbedFromKey($embedKey);

        return [
            'fields' => $embed->fields(),
            'layout' => $embed->formLayout(),
            'config' => $embed->formConfig(),
            'data' => $embed->transformDataForFormFields(request()->all()),
        ];
    }

    public function update(string $embedKey, string $entityKey, ?string $instanceId = null)
    {
        if ($instanceId) {
            sharp_check_ability('update', $entityKey, $instanceId);
        } else {
            sharp_check_ability('create', $entityKey);
        }

        $embed = $this->getEmbedFromKey($embedKey);

        $data = $embed->updateContent(
            $embed->formatRequestData(request()->all())
        );

        return $embed->transformDataForTemplate($data, true);
    }
}
