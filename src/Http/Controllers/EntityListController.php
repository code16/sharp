<?php

namespace Code16\Sharp\Http\Controllers;

use Code16\Sharp\Auth\SharpAuthorizationManager;
use Code16\Sharp\Data\BreadcrumbData;
use Code16\Sharp\Data\EntityList\EntityListData;
use Code16\Sharp\Data\NotificationData;
use Code16\Sharp\Exceptions\SharpInvalidConfigException;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
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
            'data' => $this->addMetaToItems($listData['items'], $entityKey, $listConfig),
            'meta' => $listData['meta'],
            'pageAlert' => $list->pageAlert($listData['items']),
            'config' => [
                ...$listConfig,
                'formCreateUrl' => route('code16.sharp.form.create', [
                    'parentUri' => sharp()->context()->breadcrumb()->getCurrentPath(),
                    'entityKey' => $entityKey,
                ]),
            ],
            'authorizations' => $this->getAuthorizationsForEntityList(
                $entityKey,
                $listData['items'],
                $listConfig,
            ),
            'subEntities' => $this->getSubEntitiesDataForEntityList(
                $entityKey,
                $listData['items'],
                $listConfig,
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

    private function addMetaToItems(array $listItems, string $entityKey, array $listConfig): array
    {
        $breadcrumb = sharp()->context()->breadcrumb();
        $entity = $this->entityManager->entityFor($entityKey);

        return collect($listItems)
            ->map(function ($item) use ($breadcrumb, $entity, $entityKey, $listConfig) {
                $itemSubEntity = $listConfig['subEntityAttribute'] ? ($item[$listConfig['subEntityAttribute']] ?? null) : null;
                $instanceId = $item[$listConfig['instanceIdAttribute']] ?? null;
                if ($itemSubEntity) {
                    $itemEntityKey = count($entity->getMultiforms()) > 0
                        ? "$entityKey:$itemSubEntity"
                        : $this->entityManager->entityKeyFor($entity->getSubEntityOrFail($itemSubEntity));
                } else {
                    $itemEntityKey = $entityKey;
                }
                if ($breadcrumb->getCurrentPath() && $this->sharpAuthorizationManager->isAllowed('view', $entityKey, $instanceId)) {
                    $url = $this->entityManager->entityFor($entityKey)->hasShow()
                        ? route('code16.sharp.show.show', [
                            'parentUri' => $breadcrumb->getCurrentPath(),
                            'entityKey' => $itemEntityKey,
                            'instanceId' => $item[$listConfig['instanceIdAttribute']],
                        ])
                        : route('code16.sharp.form.edit', [
                            'parentUri' => $breadcrumb->getCurrentPath(),
                            'entityKey' => $itemEntityKey,
                            'instanceId' => $item[$listConfig['instanceIdAttribute']],
                        ]);
                }

                return [
                    ...$item,
                    '_meta' => [
                        'url' => $url ?? null,
                    ],
                ];
            })
            ->all();
    }

    private function getAuthorizationsForEntityList(string $entityKey, array $listItems, array $listConfig): array
    {
        $authorizations = [
            'view' => [],
            'reorder' => $this->sharpAuthorizationManager->isAllowed('reorder', $entityKey),
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
                if ($this->sharpAuthorizationManager->isAllowed('delete', $entityKey, $instanceId)) {
                    $authorizations['delete'][] = $instanceId;
                }
            });

        return $authorizations;
    }

    private function getSubEntitiesDataForEntityList(string $entityKey, array $listItems, array $listConfig): ?array
    {
        if ($listConfig['subEntityAttribute'] === null) {
            return null;
        }

        $forms = $this->entityManager->entityFor($entityKey)->getMultiforms();
        $subEntities = $this->entityManager->entityFor($entityKey)->getSubEntities();

        if (! $forms && ! $subEntities) {
            throw new SharpInvalidConfigException(
                'The list for the entity ['.$entityKey.'] defines a sub-entity attribute ['
                .$listConfig['subEntityAttribute']
                .'] but the entity is has no sub-entities.'
            );
        }

        if ($forms) {
            return collect($forms)
                ->map(fn ($value, $key) => [
                    'key' => $key,
                    'entityKey' => "$entityKey:$key",
                    'label' => is_array($value) && count($value) > 1 ? $value[1] : $key,
                    'icon' => null,
                    'formCreateUrl' => route('code16.sharp.form.create', [
                        'parentUri' => sharp()->context()->breadcrumb()->getCurrentPath(),
                        'entityKey' => "$entityKey:$key",
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
            ->values()
            ->all();
    }
}
