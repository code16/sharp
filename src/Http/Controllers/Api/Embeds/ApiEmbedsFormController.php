<?php

namespace Code16\Sharp\Http\Controllers\Api\Embeds;

use Code16\Sharp\Data\Embeds\EmbedFormData;
use Code16\Sharp\Http\Controllers\Controller;
use Code16\Sharp\Utils\Uploads\UploadPathGuard;

class ApiEmbedsFormController extends Controller
{
    use HandlesEmbed;

    public function show(string $globalFilter, string $embedKey, string $entityKey, ?string $instanceId = null)
    {
        if ($instanceId) {
            $this->authorizationManager->check('view', $entityKey, $instanceId);
        } else {
            $this->authorizationManager->check('entity', $entityKey);
        }

        $embed = $this->getEmbedFromKey($embedKey);

        // Embed data is sent by the client: existing files must have been signed by the server
        $data = app(UploadPathGuard::class)->ensureEmbedDataIsTrusted($embed, request()->all());

        return EmbedFormData::from([
            'fields' => $embed->fields(),
            'layout' => $embed->formLayout(),
            'data' => $embed->applyFormatters(
                $embed->transformDataForFormFields($data)
            ),
        ]);
    }

    public function update(string $globalFilter, string $embedKey, string $entityKey, ?string $instanceId = null)
    {
        if ($instanceId) {
            $this->authorizationManager->check('update', $entityKey, $instanceId);
        } else {
            $this->authorizationManager->check('create', $entityKey);
        }

        $embed = $this->getEmbedFromKey($embedKey);
        $guard = app(UploadPathGuard::class);

        $data = $embed->updateContent(
            $embed->formatRequestData(
                $guard->ensureEmbedDataIsTrusted($embed, request()->all())
            )
        );

        // Files have been checked (or freshly uploaded): sign them again for the next client round-trip
        $data = $guard->signEmbedData($embed, $data);

        return $embed->transformDataWithRenderedTemplate($data, isForm: true);
    }
}
