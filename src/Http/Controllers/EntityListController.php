<?php

namespace Code16\Sharp\Http\Controllers;

use Code16\Sharp\Data\BreadcrumbData;
use Code16\Sharp\Data\EntityList\EntityListData;
use Code16\Sharp\Data\NotificationData;
use Code16\Sharp\EntityList\EntityListEntity;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Exceptions\SharpInvalidConfigException;
use Code16\Sharp\Utils\Entities\ValueObjects\EntityKey;
use Code16\Sharp\Utils\Icons\IconManager;
use Code16\Sharp\Utils\Menu\SharpMenuManager;
use Inertia\Inertia;

class EntityListController extends SharpProtectedController
{
    use HandlesEntityListItems;
    use HandlesSharpNotificationsInRequest;

    public function show(string $entityKey)
    {
        $this->authorizationManager->check('entity', $entityKey);

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
                'reorder' => $this->authorizationManager->isAllowed('reorder', $entityKey),
                'create' => $this->authorizationManager->isAllowed('create', $entityKey),
            ],
            'entities' => $this->getEntitiesDataForEntityList(
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

    private function getEntitiesDataForEntityList(string $entityKey, SharpEntityList $list): ?array
    {
        if ($list->getEntityAttribute() === null) {
            return null;
        }

        $forms = $this->entityManager->entityFor($entityKey)->getMultiforms();
        $entities = $list->getEntities()?->all();

        if (! $forms && ! $entities) {
            throw new SharpInvalidConfigException(
                'The list for the entity ['.$entityKey.'] defines a sub-entity attribute ['
                .$list->getEntityAttribute()
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

        return collect($entities)
            ->map(function (EntityListEntity $listEntity, $key) {
                $entity = $listEntity->getEntity();
                $entityKey = $this->entityManager->entityKeyFor($entity);

                if (! $this->authorizationManager->isAllowed('create', $entityKey)) {
                    return null;
                }

                return [
                    'key' => $key,
                    'entityKey' => $entityKey,
                    'label' => $listEntity->getLabel() ?: $entity->getLabelOrFail(),
                    'icon' => app(IconManager::class)->iconToArray($listEntity->getIcon()),
                    'formCreateUrl' => route('code16.sharp.form.create', [
                        'parentUri' => sharp()->context()->breadcrumb()->getCurrentPath(),
                        'entityKey' => $entityKey,
                    ]),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }
}
