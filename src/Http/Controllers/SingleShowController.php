<?php

namespace Code16\Sharp\Http\Controllers;

use Code16\Sharp\Data\BreadcrumbData;
use Code16\Sharp\Data\NotificationData;
use Code16\Sharp\Data\Show\ShowData;
use Code16\Sharp\Utils\Entities\ValueObjects\EntityKey;
use Inertia\Inertia;

class SingleShowController extends SharpProtectedController
{
    use HandlesSharpNotificationsInRequest;
    use PreloadsShowFields;

    public function show(string $filterKey, EntityKey $entityKey)
    {
        $this->authorizationManager->check('view', $entityKey);

        $entity = $this->entityManager->entityFor($entityKey);
        $show = $entity->getShowOrFail();

        $show->buildShowConfig();
        $showData = $show->instance(null);

        $payload = ShowData::from([
            'title' => $showData[$show->titleAttribute()] ?? $entity->getLabelOrFail($entityKey->multiformKey()),
            'config' => $show->showConfig(null),
            'fields' => $show->fields(),
            'layout' => $show->showLayout(),
            'data' => $show->applyFormatters($showData),
            'pageAlert' => $show->pageAlert($showData),
            'locales' => $show->hasDataLocalizations()
                ? $show->getDataLocalizations()
                : null,
            'authorizations' => [
                'create' => false,
                'view' => $this->authorizationManager->isAllowed('view', $entityKey),
                'update' => $this->authorizationManager->isAllowed('update', $entityKey),
                'delete' => false,
            ],
        ]);

        sharp()->context()
            ->breadcrumb()
            ->setCurrentInstanceLabel($showData[$show->getBreadcrumbCustomLabelAttribute()] ?? null);

        $this->addPreloadHeadersForShowEntityLists($payload);

        return Inertia::render('Show/Show', [
            'show' => $payload,
            'breadcrumb' => BreadcrumbData::from([
                'items' => sharp()->context()->breadcrumb()->allSegments(),
            ]),
            'notifications' => NotificationData::collection($this->getSharpNotifications()),
        ]);
    }
}
