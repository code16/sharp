<?php

namespace Code16\Sharp\Http\Controllers;

use Code16\Sharp\Data\BreadcrumbData;
use Code16\Sharp\Data\Form\FormData;
use Code16\Sharp\Exceptions\Form\SharpFormUpdateException;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Form\SharpSingleForm;
use Code16\Sharp\Utils\Entities\ValueObjects\EntityKey;
use Code16\Sharp\Utils\Uploads\SharpUploadManager;
use Illuminate\Support\Uri;
use Inertia\Inertia;

class FormController extends SharpProtectedController
{
    use HandlesSharpNotificationsInRequest;

    public function __construct(
        private readonly SharpUploadManager $uploadManager,
    ) {
        parent::__construct();
    }

    public function create(string $globalFilter, string $parentUri, EntityKey $entityKey)
    {
        $entity = $this->entityManager->entityFor($entityKey);

        $form = $entity->getFormOrFail($entityKey->multiformKey());

        if ($form instanceof SharpSingleForm) {
            // There is no creation in SingleForms
            return $this->edit($globalFilter, $parentUri, $entityKey);
        }

        $this->authorizationManager->check('create', $entityKey);
        $form->buildFormConfig();
        $formData = $form->newInstance();

        return Inertia::render('Form/Form', [
            'form' => FormData::from([
                ...$this->buildFormData($form, $formData, $entityKey),
                'title' => $form->getCreateTitle() ?: trans('sharp::breadcrumb.form.create_entity', [
                    'entity' => $entity->getLabelOrFail($entityKey->multiformKey()),
                ]),
            ]),
            'breadcrumb' => BreadcrumbData::from([
                'items' => sharp()->context()->breadcrumb()->allSegments(),
            ]),
            'cancelUrl' => sharp()->context()->breadcrumb()->getPreviousSegmentUrl(),
            'endpointUrl' => route('code16.sharp.form.store', [
                'parentUri' => $parentUri,
                'entityKey' => $entityKey,
                'previous_page_url' => $this->getPreviousPageUrlFromReferer(),
            ]),
        ]);
    }

    public function edit(string $globalFilter, string $parentUri, EntityKey $entityKey, ?string $instanceId = null)
    {
        $entity = $this->entityManager->entityFor($entityKey);

        $this->authorizationManager->check(
            $entity->hasShow() ? 'update' : 'view',
            $entityKey,
            $instanceId
        );

        $form = $entity->getFormOrFail($entityKey->multiformKey());

        abort_if(
            (! $instanceId && ! $form instanceof SharpSingleForm)
            || ($instanceId && $form instanceof SharpSingleForm),
            404,
        );

        $form->buildFormConfig();

        $formData = $form instanceof SharpSingleForm || $instanceId !== null
            ? $form->instance($instanceId)
            : $form->newInstance();

        if ($breadcrumbLabel = $formData[$form->getBreadcrumbCustomLabelAttribute()] ?? false) {
            sharp()->context()->breadcrumb()->setCurrentInstanceLabel($breadcrumbLabel);
            $titleEntityLabel = $breadcrumbLabel;
        }

        $titleEntityLabel ??= sharp()
            ->context()
            ->breadcrumb()
            ->getParentShowCachedBreadcrumbLabel() ?: $entity->getLabelOrFail($entityKey->multiformKey());

        return Inertia::render('Form/Form', [
            'form' => FormData::from([
                ...$this->buildFormData($form, $formData, $entityKey, $instanceId),
                'title' => $form->getEditTitle() ?: trans('sharp::breadcrumb.form.edit_entity', [
                    'entity' => $titleEntityLabel,
                ]),
            ]),
            'breadcrumb' => BreadcrumbData::from([
                'items' => sharp()->context()->breadcrumb()->allSegments(),
            ]),
            'cancelUrl' => $this->previousUrlWithHighlightedQuery(
                sharp()->context()->breadcrumb()->getPreviousSegmentUrl(),
                $entityKey,
                $instanceId
            ),
            'endpointUrl' => route('code16.sharp.form.update', [
                'parentUri' => $parentUri,
                'entityKey' => $entityKey,
                'instanceId' => $instanceId,
                'previous_page_url' => $this->getPreviousPageUrlFromReferer(),
            ]),
        ]);
    }

    public function update(string $globalFilter, string $parentUri, EntityKey $entityKey, ?string $instanceId = null)
    {
        $this->authorizationManager->check('update', $entityKey, $instanceId);

        $form = $this->entityManager
            ->entityFor($entityKey)
            ->getFormOrFail($entityKey->multiformKey());

        abort_if(
            (! $instanceId && ! $form instanceof SharpSingleForm)
            || ($instanceId && $form instanceof SharpSingleForm),
            404,
        );

        $formattedData = $form->formatAndValidateRequestData(request()->all(), $instanceId);
        $instanceId = $form->update($instanceId, $formattedData);

        if ($instanceId === null && ! $form instanceof SharpSingleForm) {
            report(new SharpFormUpdateException('The update() method in '.get_class($form).' must return the newly created instance id'));
        }

        $this->uploadManager->dispatchJobs($instanceId);

        $previousUrl = request()->query('previous_page_url') ?: sharp()->context()->breadcrumb()->getPreviousSegmentUrl();

        return redirect()->to($this->previousUrlWithHighlightedQuery($previousUrl, $entityKey, $instanceId));
    }

    public function store(string $globalFilter, string $parentUri, EntityKey $entityKey)
    {
        $form = $this->entityManager
            ->entityFor($entityKey)
            ->getFormOrFail($entityKey->multiformKey());

        if ($form instanceof SharpSingleForm) {
            // There is no creation in SingleForms
            return $this->update($globalFilter, $parentUri, $entityKey);
        }

        $this->authorizationManager->check('create', $entityKey);
        $form->buildFormConfig();
        $formattedData = $form->formatAndValidateRequestData(request()->all());
        $instanceId = $form->update(null, $formattedData);

        if ($instanceId === null) {
            report(new SharpFormUpdateException('The update() method in '.get_class($form).' must return the newly created instance id'));
        }

        $this->uploadManager->dispatchJobs($instanceId);

        $previousUrl = sharp()->context()->breadcrumb()->getPreviousSegmentUrl();

        return redirect()->to(
            $form->isDisplayShowPageAfterCreation()
                ? sprintf(
                    '%s/s-show/%s/%s',
                    $previousUrl,
                    $entityKey,
                    $instanceId
                )
                : $this->previousUrlWithHighlightedQuery(
                    $previousUrl,
                    $entityKey,
                    $instanceId
                )
        );
    }

    private function previousUrlWithHighlightedQuery(string $url, EntityKey $entityKey, ?string $instanceId): string
    {
        return Uri::of($url)->when($instanceId)->withQuery([
            'highlighted_entity_key' => $entityKey->baseKey(),
            'highlighted_instance_id' => $instanceId,
        ]);
    }

    private function getPreviousPageUrlFromReferer(): ?string
    {
        if (! request()->header('referer')) {
            return null;
        }

        $referer = Uri::of(request()->header('referer'));
        $previousSegmentUrl = Uri::of(sharp()->context()->breadcrumb()->getPreviousSegmentUrl());

        return $referer->path() === $previousSegmentUrl->path()
            ? $referer->value()
            : null;
    }

    private function buildFormData(SharpForm $form, array $formData, string $entityKey, $instanceId = null): array
    {
        return [
            'fields' => $form->fields(),
            'layout' => $form->formLayout(),
            'config' => $form->formConfig(),
            'data' => $form->applyFormatters($formData),
            'pageAlert' => $form->pageAlert($formData),
            'locales' => $form->hasDataLocalizations()
                ? $form->getDataLocalizations()
                : [],
            'authorizations' => [
                'create' => $this->authorizationManager->isAllowed('create', $entityKey),
                'view' => $this->authorizationManager->isAllowed('view', $entityKey, $instanceId),
                'update' => $this->authorizationManager->isAllowed('update', $entityKey, $instanceId),
                'delete' => $this->authorizationManager->isAllowed('delete', $entityKey, $instanceId),
            ],
        ];
    }
}
