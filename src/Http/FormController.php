<?php

namespace Code16\Sharp\Http;

use Code16\Sharp\Auth\SharpAuthorizationManager;
use Code16\Sharp\Data\BreadcrumbData;
use Code16\Sharp\Form\SharpSingleForm;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Inertia\Inertia;

class FormController extends SharpProtectedController
{
    use HandlesSharpNotificationsInRequest;

    public function __construct(
        private SharpAuthorizationManager $sharpAuthorizationManager,
        private SharpEntityManager $entityManager,
    ) {
        parent::__construct();
    }

    public function create(string $uri, string $entityKey)
    {
        sharp_check_ability(
            $this->entityManager->entityFor($entityKey)->hasShow() ? 'update' : 'view',
            $entityKey,
            $instanceId
        );

        $form = $this->entityManager->entityFor($entityKey)->getFormOrFail(sharp_normalize_entity_key($entityKey)[1]);

        if($form instanceof SharpSingleForm) {
            return $this->edit($uri, $entityKey);
        }

        $form->buildFormConfig();

        $data = [
            'fields' => $form->fields(),
            'layout' => $form->formLayout(),
            'config' => $form->formConfig(),
            'data' => $form->newInstance(),
            'locales' => $form->hasDataLocalizations()
                ? $form->getDataLocalizations()
                : null,
            'authorizations' => [
                'create' => $this->sharpAuthorizationManager->isAllowed('create', $entityKey),
                'view' => $this->sharpAuthorizationManager->isAllowed('view', $entityKey),
                'update' => $this->sharpAuthorizationManager->isAllowed('update', $entityKey),
                'delete' => $this->sharpAuthorizationManager->isAllowed('delete', $entityKey),
            ],
        ];

        // TODO handle breadcrumb

        return Inertia::render('Form', [
            'form' => $data,
            'breadcrumb' => BreadcrumbData::from(['items' => []]), // TODO
        ]);
    }

    public function edit(string $uri, string $entityKey, string $instanceId = null)
    {
        sharp_check_ability(
            $this->entityManager->entityFor($entityKey)->hasShow() ? 'update' : 'view',
            $entityKey,
            $instanceId
        );

        $form = $this->entityManager->entityFor($entityKey)->getFormOrFail(sharp_normalize_entity_key($entityKey)[1]);

        abort_if(
            (! $instanceId && ! $form instanceof SharpSingleForm)
            || ($instanceId && $form instanceof SharpSingleForm),
            404,
        );

        $form->buildFormConfig();

        $data = [
            'fields' => $form->fields(),
            'layout' => $form->formLayout(),
            'config' => $form->formConfig(),
            'data' => $form->instance($instanceId),
            'locales' => $form->hasDataLocalizations()
                ? $form->getDataLocalizations()
                : null,
            'authorizations' => [
                'create' => $this->sharpAuthorizationManager->isAllowed('create', $entityKey),
                'view' => $this->sharpAuthorizationManager->isAllowed('view', $entityKey, $instanceId),
                'update' => $this->sharpAuthorizationManager->isAllowed('update', $entityKey, $instanceId),
                'delete' => $this->sharpAuthorizationManager->isAllowed('delete', $entityKey, $instanceId),
            ],
        ];

        // TODO handle breadcrumb

        return Inertia::render('Form', [
            'form' => $data,
            'breadcrumb' => BreadcrumbData::from(['items' => [], 'visible' => false]), // TODO
        ]);
    }

    public function update(string $entityKey, string $instanceId = null)
    {
        sharp_check_ability('update', $entityKey, $instanceId);

        $form = $this->entityManager->entityFor($entityKey)->getFormOrFail(sharp_normalize_entity_key($entityKey)[1]);
        $this->checkFormImplementation($form, $instanceId);

        $form->validateRequest($entityKey);

        $form->updateInstance($instanceId, request()->all());

        return redirect()->to($this->currentSharpRequest->getUrlOfPreviousBreadcrumbItem());
    }

    public function store(string $entityKey)
    {
        $form = $this->entityManager->entityFor($entityKey)->getFormOrFail(sharp_normalize_entity_key($entityKey)[1]);

        if ($form instanceof SharpSingleForm) {
            // There is no creation in SingleForms
            return $this->update($entityKey);
        }

        sharp_check_ability('create', $entityKey);
        $form->buildFormConfig();

        $form->validateRequest($entityKey);
        $instanceId = $form->storeInstance(request()->all());

        $previousUrl = $this->currentSharpRequest->getUrlOfPreviousBreadcrumbItem();

        return redirect()
            ->to($form->isDisplayShowPageAfterCreation()
                ? "{$previousUrl}/s-show/{$entityKey}/{$instanceId}"
                : $previousUrl);
    }
}
