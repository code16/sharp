<?php

namespace Code16\Sharp\Http;

use Code16\Sharp\Auth\SharpAuthorizationManager;
use Code16\Sharp\Data\BreadcrumbData;
use Code16\Sharp\Data\EntityList\EntityListData;
use Code16\Sharp\Data\NotificationData;
use Code16\Sharp\Exceptions\SharpInvalidConfigException;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\SharpBreadcrumb;
use Inertia\Inertia;

class EntityListController extends SharpProtectedController
{
    use HandlesSharpNotificationsInRequest;

    public function __construct(
        private readonly SharpAuthorizationManager $sharpAuthorizationManager,
        private readonly SharpEntityManager $entityManager,
    ) {
        parent::__construct();
    }

    public function show(string $entityKey)
    {
        sharp_check_ability('entity', $entityKey);

        $list = $this->entityManager->entityFor($entityKey)->getListOrFail();
        $list->buildListConfig();
        $list->initQueryParams();

        $listData = $list->data();
        $listConfig = $list->listConfig($this->entityManager->entityFor($entityKey)->hasShow());

        $data = [
            'fields' => $list->fields(),
            'data' => $listData['items'],
            'meta' => $listData['meta'],
            'pageAlert' => $list->pageAlert($listData['items']),
            'config' => $listConfig,
            'authorizations' => $this->getAuthorizationsForEntityList(
                $entityKey,
                $listData['items'],
                $listConfig,
            ),
            'forms' => $this->getMultiformDataForEntityList(
                $entityKey,
                $listData['items'],
                $listConfig,
            ),
        ];

        if(request()->expectsJson()) {
            // EEL case, need to return JSON
            return response()->json(EntityListData::from($data));
        }

        return Inertia::render('EntityList/EntityList', [
            'entityList' => EntityListData::from($data),
            'breadcrumb' => BreadcrumbData::from([
                'items' => app(SharpBreadcrumb::class)->getItems($data),
            ]),
            'notifications' => NotificationData::collection($this->getSharpNotifications()),
        ]);
    }

    private function getAuthorizationsForEntityList(string $entityKey, array $listItems, array $listConfig): array
    {
        $authorizations = [
            'view' => [],
            'update' => [],
            'delete' => [],
            'create' => $this->sharpAuthorizationManager->isAllowed('create', $entityKey),
        ];

        // Collect instanceIds from response
        collect($listItems)
            ->pluck($listConfig['instanceIdAttribute'])
            ->each(function ($instanceId) use (&$authorizations, $entityKey) {
                if ($this->sharpAuthorizationManager->isAllowed('view', $entityKey, $instanceId)) {
                    $authorizations['view'][] = $instanceId;
                }
                if ($this->sharpAuthorizationManager->isAllowed('update', $entityKey, $instanceId)) {
                    $authorizations['update'][] = $instanceId;
                }
                if ($this->sharpAuthorizationManager->isAllowed('delete', $entityKey, $instanceId)) {
                    $authorizations['delete'][] = $instanceId;
                }
            });

        return $authorizations;
    }

    private function getMultiformDataForEntityList(string $entityKey, array $listItems, array $listConfig): ?array
    {
        if ($listConfig['multiformAttribute'] === null) {
            return null;
        }

        if (! $forms = $this->entityManager->entityFor($entityKey)->getMultiforms()) {
            throw new SharpInvalidConfigException(
                'The list for the entity ['.$entityKey.'] defines a multiform attribute ['
                .$listConfig['multiformAttribute']
                .'] but the entity is not configured as multiform.'
            );
        }

        return collect($forms)
            ->map(fn ($value, $key) => [
                'key' => $key,
                'label' => is_array($value) && sizeof($value) > 1 ? $value[1] : $key,
                'instances' => collect($listItems)
                    ->where($listConfig['multiformAttribute'], $key)
                    ->pluck($listConfig['instanceIdAttribute'])
                    ->toArray(),
            ])
            ->keyBy('key')
            ->all();
    }
}
