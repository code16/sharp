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
        [$entity, $form] = $this->resolveEntityAndForm($entityKey);

        if ($form instanceof SharpSingleForm) {
            // There is no creation in SingleForms
            return $this->edit($globalFilter, $parentUri, $entityKey);
        }

        $this->authorizationManager->check('create', $entityKey);
        $formData = $this->buildFormInstanceData($form, null);

        return $this->renderFormView(
            form: $form,
            formData: $formData,
            entityKey: $entityKey,
            instanceId: null,
            title: $form->getCreateTitle() ?: trans('sharp::breadcrumb.form.create_entity', [
                'entity' => $entity->getLabelOrFail($entityKey->multiformKey()),
            ]),
            cancelUrl: sharp()->context()->breadcrumb()->getPreviousSegmentUrl(),
            endpointUrl: route('code16.sharp.form.store', [
                'parentUri' => $parentUri,
                'entityKey' => $entityKey,
                'previous_page_url' => $this->getPreviousPageUrlFromReferer(),
            ]),
        );
    }

    public function edit(string $globalFilter, string $parentUri, EntityKey $entityKey, ?string $instanceId = null)
    {
        [$entity, $form] = $this->resolveEntityAndForm($entityKey);

        $this->authorizationManager->check(
            $entity->hasShow() ? 'update' : 'view',
            $entityKey,
            $instanceId
        );

        $this->ensureInstanceIdMatchesForm($form, $instanceId);

        $formData = $this->buildFormInstanceData($form, $instanceId);
        $titleEntityLabel = $this->resolveTitleEntityLabel($form, $formData, $entity, $entityKey);

        return $this->renderFormView(
            form: $form,
            formData: $formData,
            entityKey: $entityKey,
            instanceId: $instanceId,
            title: $form->getEditTitle() ?: trans('sharp::breadcrumb.form.edit_entity', [
                'entity' => $titleEntityLabel,
            ]),
            cancelUrl: $this->previousUrlWithHighlightedQuery(
                sharp()->context()->breadcrumb()->getPreviousSegmentUrl(),
                $entityKey,
                $instanceId
            ),
            endpointUrl: route('code16.sharp.form.update', [
                'parentUri' => $parentUri,
                'entityKey' => $entityKey,
                'instanceId' => $instanceId,
                'previous_page_url' => $this->getPreviousPageUrlFromReferer(),
            ]),
        );
    }

    public function update(string $globalFilter, string $parentUri, EntityKey $entityKey, ?string $instanceId = null)
    {
        $this->authorizationManager->check('update', $entityKey, $instanceId);

        [, $form] = $this->resolveEntityAndForm($entityKey);
        $this->ensureInstanceIdMatchesForm($form, $instanceId);
        $instanceId = $this->persistForm($form, $instanceId);

        $previousUrl = request()->query('previous_page_url') ?: sharp()->context()->breadcrumb()->getPreviousSegmentUrl();

        return redirect()->to($this->previousUrlWithHighlightedQuery($previousUrl, $entityKey, $instanceId));
    }

    public function store(string $globalFilter, string $parentUri, EntityKey $entityKey)
    {
        [, $form] = $this->resolveEntityAndForm($entityKey);

        if ($form instanceof SharpSingleForm) {
            // There is no creation in SingleForms
            return $this->update($globalFilter, $parentUri, $entityKey);
        }

        $this->authorizationManager->check('create', $entityKey);
        $form->buildFormConfig();
        $instanceId = $this->persistForm($form, null);

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

    /**
     * @return array{0: mixed, 1: SharpForm}
     */
    private function resolveEntityAndForm(EntityKey $entityKey): array
    {
        $entity = $this->entityManager->entityFor($entityKey);

        return [$entity, $entity->getFormOrFail($entityKey->multiformKey())];
    }

    private function ensureInstanceIdMatchesForm(SharpForm $form, ?string $instanceId): void
    {
        abort_if(
            (! $instanceId && ! $form instanceof SharpSingleForm)
            || ($instanceId && $form instanceof SharpSingleForm),
            404,
        );
    }

    private function buildFormInstanceData(SharpForm $form, ?string $instanceId): array
    {
        $form->buildFormConfig();

        return $form instanceof SharpSingleForm || $instanceId !== null
            ? $form->instance($instanceId)
            : $form->newInstance();
    }

    private function resolveTitleEntityLabel(
        SharpForm $form,
        array $formData,
        mixed $entity,
        EntityKey $entityKey,
    ): string {
        if ($breadcrumbLabel = $form->getBreadcrumbCustomLabel($formData)) {
            sharp()->context()->breadcrumb()->setCurrentInstanceLabel($breadcrumbLabel);

            return $breadcrumbLabel;
        }

        return sharp()
            ->context()
            ->breadcrumb()
            ->getParentShowCachedBreadcrumbLabel()
            ?: $entity->getLabelOrFail($entityKey->multiformKey());
    }

    private function renderFormView(
        SharpForm $form,
        array $formData,
        EntityKey $entityKey,
        ?string $instanceId,
        string $title,
        string $cancelUrl,
        string $endpointUrl,
    ) {
        if (app()->environment('testing')) {
            Inertia::share('_rawData', $formData);
        }

        return Inertia::render('Form/Form', [
            'form' => FormData::from([
                ...$this->buildFormData($form, $formData, $entityKey, $instanceId),
                'title' => $title,
            ]),
            'breadcrumb' => BreadcrumbData::from([
                'items' => sharp()->context()->breadcrumb()->allSegments(),
            ]),
            'cancelUrl' => $cancelUrl,
            'endpointUrl' => $endpointUrl,
        ]);
    }

    private function persistForm(SharpForm $form, ?string $instanceId): ?string
    {
        $formattedData = $form->formatAndValidateRequestData(request()->all(), $instanceId);
        $instanceId = $form->update($instanceId, $formattedData);

        if ($instanceId === null && ! $form instanceof SharpSingleForm) {
            report(new SharpFormUpdateException('The update() method in '.get_class($form).' must return the newly created instance id'));
        }

        $this->uploadManager->dispatchJobs($instanceId);

        return $instanceId;
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
