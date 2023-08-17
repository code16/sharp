<?php

namespace Code16\Sharp\Http;

use Code16\Sharp\Auth\SharpAuthorizationManager;
use Code16\Sharp\Data\BreadcrumbData;
use Code16\Sharp\Data\NotificationData;
use Code16\Sharp\Data\Show\ShowData;
use Code16\Sharp\Show\SharpSingleShow;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Inertia\Inertia;

class ShowController extends SharpProtectedController
{
    use HandlesSharpNotificationsInRequest;

    public function __construct(
        private SharpAuthorizationManager $sharpAuthorizationManager,
        private SharpEntityManager $entityManager,
    ) {
        parent::__construct();
    }

    public function show(string $uri, string $entityKey, string $instanceId)
    {
        sharp_check_ability('view', $entityKey, $instanceId);

        $show = $this->entityManager->entityFor($entityKey)->getShowOrFail();

        abort_if(
            $show instanceof SharpSingleShow,
            404,
        );

        $show->buildShowConfig();

        $data = [
            'config' => $show->showConfig($instanceId),
            'fields' => $show->fields(),
            'layout' => $show->showLayout(),
            'data' => $show->instance($instanceId),
            'locales' => $show->hasDataLocalizations()
                ? $show->getDataLocalizations()
                : null,
            'authorizations' => [
                'create' => $this->sharpAuthorizationManager->isAllowed('create', $entityKey),
                'view' => $this->sharpAuthorizationManager->isAllowed('view', $entityKey, $instanceId),
                'update' => $this->sharpAuthorizationManager->isAllowed('update', $entityKey, $instanceId),
                'delete' => $this->sharpAuthorizationManager->isAllowed('delete', $entityKey, $instanceId),
            ],
        ];

        return Inertia::render('Show', [
            'show' => ShowData::from($data),
            'breadcrumb' => BreadcrumbData::from(['items' => []]), // TODO
            'notifications' => NotificationData::collection($this->getSharpNotifications()),
        ]);
    }
}
