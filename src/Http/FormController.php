<?php

namespace Code16\Sharp\Http;

use Code16\Sharp\Auth\SharpAuthorizationManager;
use Code16\Sharp\Data\BreadcrumbData;
use Code16\Sharp\Form\SharpSingleForm;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\SharpBreadcrumb;
use Inertia\Inertia;

class FormController extends SharpProtectedController
{
    use HandlesSharpNotificationsInRequest;

    public function __construct(
        private readonly SharpAuthorizationManager $sharpAuthorizationManager,
        private readonly SharpEntityManager $entityManager,
    ) {
        parent::__construct();
    }

    public function create(string $uri, string $entityKey)
    {
        $entity = $this->entityManager->entityFor($entityKey);
        
        sharp_check_ability(
            $entity->hasShow() ? 'create' : 'view',
            $entityKey,
        );

        $form = $entity->getFormOrFail(sharp_normalize_entity_key($entityKey)[1]);

        if($form instanceof SharpSingleForm) {
            return $this->edit($uri, $entityKey);
        }

        $form->buildFormConfig();

        $data = [
            'fields' => $form->fields(),
            'layout' => $form->formLayout(),
            'config' => $form->formConfig(),
            'data' => $form->newInstance(),
            'pageAlert' => $form->pageAlert(),
            'locales' => $form->hasDataLocalizations()
                ? $form->getDataLocalizations()
                : [],
            'authorizations' => [
                'create' => $this->sharpAuthorizationManager->isAllowed('create', $entityKey),
                'view' => $this->sharpAuthorizationManager->isAllowed('view', $entityKey),
                'update' => $this->sharpAuthorizationManager->isAllowed('update', $entityKey),
                'delete' => $this->sharpAuthorizationManager->isAllowed('delete', $entityKey),
            ],
        ];

        return Inertia::render('Form/Form', [
            'form' => $data,
            'breadcrumb' => BreadcrumbData::from([
                'items' => app(SharpBreadcrumb::class)->getItems($data)
            ]),
        ]);
    }

    public function edit(string $uri, string $entityKey, string $instanceId = null)
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

        $data = [
            'fields' => $form->fields(),
            'layout' => $form->formLayout(),
            'config' => $form->formConfig(),
            'data' => $form->instance($instanceId),
            'pageAlert' => $form->pageAlert(),
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

        return Inertia::render('Form/Form', [
            'form' => $data,
            'breadcrumb' => BreadcrumbData::from([
                'items' => app(SharpBreadcrumb::class)->getItems($data)
            ]),
        ]);
    }

    public function update(string $uri, string $entityKey, string $instanceId = null)
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

        $form->validateRequest();

        $form->updateInstance($instanceId, request()->all());

        return redirect()->to($this->currentSharpRequest->getUrlOfPreviousBreadcrumbItem());
    }

    public function store(string $uri, string $entityKey)
    {
        $form = $this->entityManager->entityFor($entityKey)->getFormOrFail(sharp_normalize_entity_key($entityKey)[1]);

        if ($form instanceof SharpSingleForm) {
            // There is no creation in SingleForms
            return $this->update($uri, $entityKey);
        }

        sharp_check_ability('create', $entityKey);
        $form->buildFormConfig();

        $form->validateRequest();
        $instanceId = $form->storeInstance(request()->all());

        $previousUrl = $this->currentSharpRequest->getUrlOfPreviousBreadcrumbItem();

        return redirect()
            ->to($form->isDisplayShowPageAfterCreation()
                ? "{$previousUrl}/s-show/{$entityKey}/{$instanceId}"
                : $previousUrl);
    }
}
