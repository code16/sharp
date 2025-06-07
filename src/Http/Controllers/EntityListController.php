<?php

namespace Code16\Sharp\Http\Controllers;

use Code16\Sharp\Data\BreadcrumbData;
use Code16\Sharp\Data\EntityList\EntityListData;
use Code16\Sharp\Data\NotificationData;
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

                if (! $this->authorizationManager->isAllowed('create', $subEntityKey)) {
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
