<?php

namespace Code16\Sharp\Http\Controllers\Api;

use Code16\Sharp\Utils\Entities\ValueObjects\EntityKey;
use Illuminate\Contracts\View\View;

class ApiFormPreviewController extends ApiController
{
    public function store(EntityKey $entityKey, ?string $instanceId = null)
    {
        $entity = $this->entityManager->entityFor($entityKey);

        $form = $entity->getFormOrFail($entityKey->multiformKey());

        $form->buildFormConfig();

        $formattedData = $form->formatAndValidateRequestData(request()->all(), $instanceId);
        $view = $form->updateForPreview($instanceId, $formattedData);

        if (! $view instanceof View) {
            throw new \Exception('A view must be returned by the update() method if $this->isPreview is true.');
        }

        return response()->json([
            'data' => [
                'html' => $view->render(),
            ],
        ]);
    }
}
