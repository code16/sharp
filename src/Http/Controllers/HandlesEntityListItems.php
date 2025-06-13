<?php

namespace Code16\Sharp\Http\Controllers;

use Code16\Sharp\Auth\SharpAuthorizationManager;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Exceptions\SharpInvalidEntityKeyException;
use Code16\Sharp\Utils\Entities\SharpEntity;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\Entities\ValueObjects\EntityKey;

trait HandlesEntityListItems
{
    private function addMetaToItems(array $listItems, string $entityKey, SharpEntityList $list): array
    {
        $entity = app(SharpEntityManager::class)->entityFor($entityKey);

        return collect($listItems)
            ->map(function ($item) use ($entity, $entityKey, $list) {
                $itemEntityKey = $this->getItemEntityKey($item, $entityKey, $entity, $list);
                $instanceId = $item[$list->getInstanceIdAttribute()] ?? null;

                return [
                    ...$item,
                    '_meta' => [
                        'url' => $this->getItemUrl($item, $entityKey, $entity, $list),
                        'authorizations' => [
                            'view' => app(SharpAuthorizationManager::class)->isAllowed('view', $itemEntityKey, $instanceId),
                            'delete' => app(SharpAuthorizationManager::class)->isAllowed('delete', $itemEntityKey, $instanceId),
                        ],
                    ],
                ];
            })
            ->all();
    }

    private function getItemEntityKey(array $item, string $entityKey, SharpEntity $entity, SharpEntityList $list): string
    {
        $itemEntityAttributeValue = $list->getEntityAttribute()
            ? ($item[$list->getEntityAttribute()] ?? null)
            : null;

        if ($itemEntityAttributeValue) {
            if (count($entity->getMultiforms()) > 0) {
                return EntityKey::multiform(baseKey: $entityKey, multiformKey: $itemEntityAttributeValue);
            }

            if (! $listEntity = ($list->getEntities()->find($itemEntityAttributeValue))) {
                throw new SharpInvalidEntityKeyException(
                    sprintf('The sub-entity [%s] for the entity-list [%s] was not found.', $itemEntityAttributeValue, get_class($list))
                );
            }

            return $listEntity->getEntityKey();
        }

        return $entityKey;
    }

    private function getItemUrl(array $item, string $entityKey, SharpEntity $entity, SharpEntityList $list): ?string
    {
        $breadcrumb = sharp()->context()->breadcrumb();

        $itemEntityKey = $this->getItemEntityKey($item, $entityKey, $entity, $list);
        $instanceId = $item[$list->getInstanceIdAttribute()] ?? null;

        if (! app(SharpAuthorizationManager::class)->isAllowed('view', $itemEntityKey, $instanceId)) {
            return null;
        }

        $itemEntity = app(SharpEntityManager::class)->entityFor($itemEntityKey);

        if ($breadcrumb->getCurrentPath()) {
            if ($itemEntity->hasShow()) {
                return route('code16.sharp.show.show', [
                    'parentUri' => $breadcrumb->getCurrentPath(),
                    'entityKey' => $itemEntityKey,
                    'instanceId' => $item[$list->getInstanceIdAttribute()],
                ]);
            } elseif ($itemEntity->hasForm()) {
                return route('code16.sharp.form.edit', [
                    'parentUri' => $breadcrumb->getCurrentPath(),
                    'entityKey' => $itemEntityKey,
                    'instanceId' => $item[$list->getInstanceIdAttribute()],
                ]);
            }
        }

        return null;
    }
}
