<?php

namespace Code16\Sharp\Http\Controllers;

use Code16\Sharp\Data\BreadcrumbData;
use Code16\Sharp\Data\NotificationData;
use Code16\Sharp\Data\Show\ShowData;
use Code16\Sharp\Show\SharpSingleShow;
use Code16\Sharp\Utils\Entities\ValueObjects\EntityKey;
use Inertia\Inertia;

class ShowController extends SharpProtectedController
{
    use HandlesSharpNotificationsInRequest;
    use PreloadsShowFields;

    public function show(string $globalFilter, string $parentUri, EntityKey $entityKey, string $instanceId)
    {
        $this->authorizationManager->check('view', $entityKey, $instanceId);

        $entity = $this->entityManager->entityFor($entityKey);
        $show = $entity->getShowOrFail();

        abort_if($show instanceof SharpSingleShow, 404);

        $show->buildShowConfig();

        $showData = $show->instance($instanceId);
        $payload = ShowData::from([
            'title' => $showData[$show->titleAttribute()] ?? $entity->getLabelOrFail($entityKey->multiformKey()),
            'config' => [
                ...$show->showConfig($instanceId),
                'formEditUrl' => route('code16.sharp.form.edit', [
                    'parentUri' => sharp()->context()->breadcrumb()->getCurrentPath(),
                    'entityKey' => $entityKey,
                    'instanceId' => $instanceId,
                ]),
            ],
            'fields' => $show->fields(),
            'layout' => $show->showLayout(),
            'data' => $show->applyFormatters($showData),
            'pageAlert' => $show->pageAlert($showData),
            'locales' => $show->hasDataLocalizations()
                ? $show->getDataLocalizations()
                : null,
            'authorizations' => [
                'create' => $this->authorizationManager->isAllowed('create', $entityKey),
                'view' => $this->authorizationManager->isAllowed('view', $entityKey, $instanceId),
                'update' => $this->authorizationManager->isAllowed('update', $entityKey, $instanceId),
                'delete' => $this->authorizationManager->isAllowed('delete', $entityKey, $instanceId),
            ],
        ]);

        sharp()->context()
            ->breadcrumb()
            ->setCurrentInstanceLabel($showData[$show->getBreadcrumbCustomLabelAttribute()] ?? false);

        $this->addPreloadHeadersForShowEntityLists($payload);

        if (app()->environment('testing')) {
            Inertia::share('_rawData', $showData);
        }

        return Inertia::render('Show/Show', [
            'show' => $payload,
            'breadcrumb' => BreadcrumbData::from([
                'items' => sharp()->context()->breadcrumb()->allSegments(),
            ]),
            'notifications' => NotificationData::collection($this->getSharpNotifications()),
        ]);
    }

    public function delete(string $globalFilter, string $parentUri, string $entityKey, string $instanceId)
    {
        $this->authorizationManager->check('delete', $entityKey, $instanceId);

        $show = $this->entityManager->entityFor($entityKey)->getShowOrFail();
        $show->delete($instanceId);

        return redirect()->to(sharp()->context()->breadcrumb()->getPreviousSegmentUrl());
    }
}
