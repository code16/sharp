<?php

namespace Code16\Sharp\Http\Controllers;

use Code16\Sharp\Auth\SharpAuthorizationManager;
use Code16\Sharp\Data\BreadcrumbData;
use Code16\Sharp\Data\NotificationData;
use Code16\Sharp\Data\Show\ShowData;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Inertia\Inertia;

class SingleShowController extends SharpProtectedController
{
    use HandlesSharpNotificationsInRequest;
    use PreloadsShowEntityLists;

    public function __construct(
        private SharpAuthorizationManager $sharpAuthorizationManager,
        private SharpEntityManager $entityManager,
    ) {
        parent::__construct();
    }

    public function show(string $entityKey)
    {
        sharp_check_ability('view', $entityKey);

        $show = $this->entityManager->entityFor($entityKey)->getShowOrFail();

        $show->buildShowConfig();
        $showData = $show->instance(null);

        $payload = ShowData::from([
            'config' => $show->showConfig(null),
            'fields' => $show->fields(),
            'layout' => $show->showLayout(),
            'data' => $show->applyFormatters($showData),
            'pageAlert' => $show->pageAlert($showData),
            'locales' => $show->hasDataLocalizations()
                ? $show->getDataLocalizations()
                : null,
            'authorizations' => [
                'create' => $this->sharpAuthorizationManager->isAllowed('create', $entityKey),
                'view' => $this->sharpAuthorizationManager->isAllowed('view', $entityKey),
                'update' => $this->sharpAuthorizationManager->isAllowed('update', $entityKey),
                'delete' => $this->sharpAuthorizationManager->isAllowed('delete', $entityKey),
            ],
        ]);
        
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
