<?php

namespace Code16\Sharp\Http\Controllers;

use Code16\Sharp\Auth\SharpAuthorizationManager;
use Code16\Sharp\Data\BreadcrumbData;
use Code16\Sharp\Data\NotificationData;
use Code16\Sharp\Data\Show\ShowData;
use Code16\Sharp\Show\SharpSingleShow;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\Entities\ValueObjects\EntityKey;
use Inertia\Inertia;

class ShowController extends SharpProtectedController
{
    use HandlesSharpNotificationsInRequest;
    use PreloadsShowEntityLists;

    public function __construct(
        private readonly SharpAuthorizationManager $sharpAuthorizationManager,
        private readonly SharpEntityManager $entityManager,
    ) {
        parent::__construct();
    }

    public function show(string $parentUri, EntityKey $entityKey, string $instanceId)
    {
        sharp_check_ability('view', $entityKey, $instanceId);

        $entity = $this->entityManager->entityFor($entityKey);
        $show = $entity->getShowOrFail();

        abort_if($show instanceof SharpSingleShow, 404);

        $show->buildShowConfig();

        $showData = $show->instance($instanceId);
        $payload = ShowData::from([
            'title' => $showData[$show->titleAttribute()] ?? $entity->getLabelOrFail($entityKey->subEntity()),
            'config' => $show->showConfig($instanceId),
            'fields' => $show->fields(),
            'layout' => $show->showLayout(),
            'data' => $show->applyFormatters($showData),
            'pageAlert' => $show->pageAlert($showData),
            'locales' => $show->hasDataLocalizations()
                ? $show->getDataLocalizations()
                : null,
            'authorizations' => [
                'create' => $this->sharpAuthorizationManager->isAllowed('create', $entityKey),
                'view' => $this->sharpAuthorizationManager->isAllowed('view', $entityKey, $instanceId),
                'update' => $this->sharpAuthorizationManager->isAllowed('update', $entityKey, $instanceId),
                'delete' => $this->sharpAuthorizationManager->isAllowed('delete', $entityKey, $instanceId),
            ],
        ]);

        if ($breadcrumbAttr = $showData[$payload->config->breadcrumbAttribute] ?? false) {
            sharp()->context()->breadcrumb()->setCurrentInstanceLabel($breadcrumbAttr);
        }

        $this->addPreloadHeadersForShowEntityLists($payload);

        return Inertia::render('Show/Show', [
            'show' => $payload,
            'breadcrumb' => BreadcrumbData::from([
                'items' => sharp()->context()->breadcrumb()->allSegments(),
            ]),
            'notifications' => NotificationData::collection($this->getSharpNotifications()),
        ]);
    }

    public function delete(string $parentUri, string $entityKey, string $instanceId)
    {
        sharp_check_ability('delete', $entityKey, $instanceId);

        $show = $this->entityManager->entityFor($entityKey)->getShowOrFail();

        $show->delete($instanceId);

        return redirect()->to(sharp()->context()->breadcrumb()->getPreviousSegmentUrl());
    }
}
