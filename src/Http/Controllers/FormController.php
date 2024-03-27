<?php

namespace Code16\Sharp\Http\Controllers;

use Code16\Sharp\Auth\SharpAuthorizationManager;
use Code16\Sharp\Data\BreadcrumbData;
use Code16\Sharp\Data\Form\FormData;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Form\SharpSingleForm;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\SharpBreadcrumb;
use Code16\Sharp\Utils\Uploads\SharpUploadManager;
use Inertia\Inertia;

class FormController extends SharpProtectedController
{
    use HandlesSharpNotificationsInRequest;

    public function __construct(
        private readonly SharpAuthorizationManager $sharpAuthorizationManager,
        private readonly SharpEntityManager $entityManager,
        private readonly SharpUploadManager $uploadManager,
    ) {
        parent::__construct();
    }

    public function create(string $parentUri, string $entityKey)
    {
        $entity = $this->entityManager->entityFor($entityKey);

        sharp_check_ability(
            $entity->hasShow() ? 'create' : 'view',
            $entityKey,
        );

        $form = $entity->getFormOrFail(sharp_normalize_entity_key($entityKey)[1]);

        if ($form instanceof SharpSingleForm) {
            // There is no creation in SingleForms
            return $this->edit($parentUri, $entityKey);
        }

        $form->buildFormConfig();
        $data = $this->buildFormData($form, $entityKey);

        return Inertia::render('Form/Form', [
            'form' => FormData::from($data),
            'breadcrumb' => BreadcrumbData::from([
                'items' => app(SharpBreadcrumb::class)->getItems($data),
            ]),
        ]);
    }

    public function edit(string $parentUri, string $entityKey, string $instanceId = null)
    {
        $entity = $this->entityManager->entityFor($entityKey);

        sharp_check_ability(
            $entity->hasShow() ? 'update' : 'view',
            $entityKey,
            $instanceId
        );

        $form = $entity->getFormOrFail(sharp_normalize_entity_key($entityKey)[1]);

        abort_if(
            (! $instanceId && ! $form instanceof SharpSingleForm)
            || ($instanceId && $form instanceof SharpSingleForm),
            404,
        );

        $form->buildFormConfig();
        $data = $this->buildFormData($form, $entityKey, $instanceId);

        return Inertia::render('Form/Form', [
            'form' => FormData::from($data),
            'breadcrumb' => BreadcrumbData::from([
                'items' => app(SharpBreadcrumb::class)->getItems($data),
            ]),
        ]);
    }

    public function update(string $parentUri, string $entityKey, string $instanceId = null)
    {
        sharp_check_ability('update', $entityKey, $instanceId);

        $form = $this->entityManager
            ->entityFor($entityKey)
            ->getFormOrFail(sharp_normalize_entity_key($entityKey)[1]);

        abort_if(
            (! $instanceId && ! $form instanceof SharpSingleForm)
            || ($instanceId && $form instanceof SharpSingleForm),
            404,
        );

        $formattedData = $form->formatAndValidateRequestData(request()->all(), $instanceId);
        $form->update($instanceId, $formattedData);
        $this->uploadManager->dispatchJobs($instanceId);

        return redirect()->to($this->currentSharpRequest->getUrlOfPreviousBreadcrumbItem());
    }

    public function store(string $parentUri, string $entityKey)
    {
        $form = $this->entityManager->entityFor($entityKey)->getFormOrFail(sharp_normalize_entity_key($entityKey)[1]);

        if ($form instanceof SharpSingleForm) {
            // There is no creation in SingleForms
            return $this->update($parentUri, $entityKey);
        }

        sharp_check_ability('create', $entityKey);
        $form->buildFormConfig();

        $formattedData = $form->formatAndValidateRequestData(request()->all());
        $instanceId = $form->update(null, $formattedData);
        $this->uploadManager->dispatchJobs($instanceId);

        $previousUrl = $this->currentSharpRequest->getUrlOfPreviousBreadcrumbItem();

        return redirect()->to(
            $form->isDisplayShowPageAfterCreation()
                ? sprintf(
                    '%s/s-show/%s/%s',
                    $previousUrl,
                    sharp_normalize_entity_key($entityKey)[0],
                    $instanceId
                )
                : $previousUrl
        );
    }

    private function buildFormData(SharpForm $form, string $entityKey, $instanceId = null): array
    {
        $formData = $form instanceof SharpSingleForm || $instanceId !== null
            ? $form->instance($instanceId)
            : $form->newInstance();

        return [
            'fields' => $form->fields(),
            'layout' => $form->formLayout(),
            'config' => $form->formConfig(),
            'data' => $formData,
            'pageAlert' => $form->pageAlert($formData),
            'locales' => $form->hasDataLocalizations()
                ? $form->getDataLocalizations()
                : [],
            'authorizations' => [
                'create' => $this->sharpAuthorizationManager->isAllowed('create', $entityKey),
                'view' => $this->sharpAuthorizationManager->isAllowed('view', $entityKey, $instanceId),
                'update' => $this->sharpAuthorizationManager->isAllowed('update', $entityKey, $instanceId),
                'delete' => $this->sharpAuthorizationManager->isAllowed('delete', $entityKey, $instanceId),
            ],
        ];
    }
}
