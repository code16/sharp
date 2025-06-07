<?php

namespace Code16\Sharp\Http\Controllers\Api\Embeds;

use Code16\Sharp\Auth\SharpAuthorizationManager;
use Code16\Sharp\Data\Embeds\EmbedFormData;
use Illuminate\Routing\Controller;

class ApiEmbedsFormController extends Controller
{
    use HandlesEmbed;

    public function __construct(private readonly SharpAuthorizationManager $authorizationManager) {}

    public function show(string $embedKey, string $entityKey, ?string $instanceId = null)
    {
        if ($instanceId) {
            $this->authorizationManager->check('view', $entityKey, $instanceId);
        } else {
            $this->authorizationManager->check('entity', $entityKey);
        }

        $embed = $this->getEmbedFromKey($embedKey);

        return EmbedFormData::from([
            'fields' => $embed->fields(),
            'layout' => $embed->formLayout(),
            'data' => $embed->applyFormatters(
                $embed->transformDataForFormFields(request()->all())
            ),
        ]);
    }

    public function update(string $embedKey, string $entityKey, ?string $instanceId = null)
    {
        if ($instanceId) {
            $this->authorizationManager->check('update', $entityKey, $instanceId);
        } else {
            $this->authorizationManager->check('create', $entityKey);
        }

        $embed = $this->getEmbedFromKey($embedKey);

        $data = $embed->updateContent(
            $embed->formatRequestData(request()->all())
        );

        return $embed->transformDataWithRenderedTemplate($data, isForm: true);
    }
}
