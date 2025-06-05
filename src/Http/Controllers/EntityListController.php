<?php

namespace Code16\Sharp\Http\Controllers;

use Code16\Sharp\Auth\SharpAuthorizationManager;
use Code16\Sharp\Data\BreadcrumbData;
use Code16\Sharp\Data\EntityList\EntityListData;
use Code16\Sharp\Data\NotificationData;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Exceptions\SharpInvalidConfigException;
use Code16\Sharp\Exceptions\SharpInvalidEntityKeyException;
use Code16\Sharp\Utils\Entities\SharpEntity;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\Entities\ValueObjects\EntityKey;
use Code16\Sharp\Utils\Icons\IconManager;
use Code16\Sharp\Utils\Menu\SharpMenuManager;
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
        $list->initQueryParams(request()->query());

        $listData = $list->data(request()->query());
        $listConfig = $list->listConfig($this->entityManager->entityFor($entityKey)->hasShow());

        $data = [
            'fields' => $list->fields(),
            'data' => $this->addMetaToItems($listData['items'], $entityKey, $list),
            'meta' => $listData['meta'],
            'pageAlert' => $list->pageAlert($listData['items']),
            'config' => [
                ...$listConfig,
                'formCreateUrl' => route('code16.sharp.form.create', [
                    'parentUri' => sharp()->context()->breadcrumb()->getCurrentPath(),
                    'entityKey' => $entityKey,
                ]),
            ],
            'authorizations' => [
                'reorder' => $this->sharpAuthorizationManager->isAllowed('reorder', $entityKey),
                'create' => $this->sharpAuthorizationManager->isAllowed('create', $entityKey),
            ],
            'subEntities' => $this->getSubEntitiesDataForEntityList(
                $entityKey,
                $list
            ),
            'filterValues' => $list->filterContainer()->getCurrentFilterValuesForFront(request()->all()),
            'query' => count(request()->query()) ? request()->query() : null,
        ];

        if (request()->routeIs('code16.sharp.api.list')) {
            // EEL case, need to return JSON
            return response()->json(EntityListData::from($data)->toArray());
        }

        return Inertia::render('EntityList/EntityList', [
            'entityList' => EntityListData::from([
                ...$data,
                'title' => app(SharpMenuManager::class)
                    ->getEntityMenuItem($entityKey)
                    ?->getLabel() ?: trans('sharp::breadcrumb.entityList'),
            ]),
            'breadcrumb' => BreadcrumbData::from([
                'items' => sharp()->context()->breadcrumb()->allSegments(),
            ]),
            'notifications' => NotificationData::collection($this->getSharpNotifications()),
        ]);
    }

    private function addMetaToItems(array $listItems, string $entityKey, SharpEntityList $list): array
    {
        $entity = $this->entityManager->entityFor($entityKey);

        return collect($listItems)
            ->map(function ($item) use ($entity, $entityKey, $list) {
                $itemEntityKey = $this->getItemEntityKey($item, $entityKey, $entity, $list);
                $instanceId = $item[$list->getInstanceIdAttribute()] ?? null;

                return [
                    ...$item,
                    '_meta' => [
                        'url' => $this->getItemUrl($item, $entityKey, $entity, $list),
                        'authorizations' => [
                            'view' => $this->sharpAuthorizationManager->isAllowed('view', $itemEntityKey, $instanceId),
                            'delete' => $this->sharpAuthorizationManager->isAllowed('delete', $itemEntityKey, $instanceId),
                        ],
                    ],
                ];
            })
            ->all();
    }

    private function getItemEntityKey(array $item, string $entityKey, SharpEntity $entity, SharpEntityList $list): string
    {
        $itemSubEntity = $list->getSubEntityAttribute() ? ($item[$list->getSubEntityAttribute()] ?? null) : null;

        if ($itemSubEntity) {
            if (count($entity->getMultiforms()) > 0) {
                return EntityKey::multiform(baseKey: $entityKey, multiformKey: $itemSubEntity);
            }

            if (! $itemSubEntityClass = ($list->getSubEntities()[$itemSubEntity] ?? null)) {
                throw new SharpInvalidEntityKeyException(
                    sprintf('The sub-entity [%s] for the entity [%s] was not found.', $itemSubEntity, get_class($this))
                );
            }

            return $this->entityManager->entityKeyFor($itemSubEntityClass);
        }

        return $entityKey;
    }

    private function getItemUrl(array $item, string $entityKey, SharpEntity $entity, SharpEntityList $list): ?string
    {
        $breadcrumb = sharp()->context()->breadcrumb();

        $itemEntityKey = $this->getItemEntityKey($item, $entityKey, $entity, $list);
        $instanceId = $item[$list->getInstanceIdAttribute()] ?? null;

        if (! $this->sharpAuthorizationManager->isAllowed('view', $itemEntityKey, $instanceId)) {
            return null;
        }

        $itemEntity = $this->entityManager->entityFor($itemEntityKey);

        if ($breadcrumb->getCurrentPath()) {
            return $itemEntity->hasShow()
                ? route('code16.sharp.show.show', [
                    'parentUri' => $breadcrumb->getCurrentPath(),
                    'entityKey' => $itemEntityKey,
                    'instanceId' => $item[$list->getInstanceIdAttribute()],
                ])
                : route('code16.sharp.form.edit', [
                    'parentUri' => $breadcrumb->getCurrentPath(),
                    'entityKey' => $itemEntityKey,
                    'instanceId' => $item[$list->getInstanceIdAttribute()],
                ]);
        }

        return null;
    }

    private function getSubEntitiesDataForEntityList(string $entityKey, SharpEntityList $list): ?array
    {
        if ($list->getSubEntityAttribute() === null) {
            return null;
        }

        $forms = $this->entityManager->entityFor($entityKey)->getMultiforms();
        $subEntities = $list->getSubEntities();

        if (! $forms && ! $subEntities) {
            throw new SharpInvalidConfigException(
                'The list for the entity ['.$entityKey.'] defines a sub-entity attribute ['
                .$list->getSubEntityAttribute()
                .'] but the entity is has no sub-entities.'
            );
        }

        if ($forms) {
            return collect($forms)
                ->map(fn ($value, $key) => [
                    'key' => $key,
                    'entityKey' => EntityKey::multiform(baseKey: $entityKey, multiformKey: $key),
                    'label' => is_array($value) && count($value) > 1 ? $value[1] : $key,
                    'icon' => null,
                    'formCreateUrl' => route('code16.sharp.form.create', [
                        'parentUri' => sharp()->context()->breadcrumb()->getCurrentPath(),
                        'entityKey' => EntityKey::multiform(baseKey: $entityKey, multiformKey: $key),
                    ]),
                ])
                ->whereNotNull('label')
                ->values()
                ->all();
        }

        return collect($subEntities)
            ->map(function ($subEntityClass, $key) {
                $subEntityKey = $this->entityManager->entityKeyFor($subEntityClass);
                $subEntity = $this->entityManager->entityFor($subEntityKey);

                if (! $this->sharpAuthorizationManager->isAllowed('create', $subEntityKey)) {
                    return null;
                }

                return [
                    'key' => $key,
                    'entityKey' => $subEntityKey,
                    'label' => $subEntity->getLabelOrFail(),
                    'icon' => app(IconManager::class)->iconToArray($subEntity->getIcon()),
                    'formCreateUrl' => route('code16.sharp.form.create', [
                        'parentUri' => sharp()->context()->breadcrumb()->getCurrentPath(),
                        'entityKey' => $subEntityKey,
                    ]),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }
}
