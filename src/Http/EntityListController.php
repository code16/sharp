<?php

namespace Code16\Sharp\Http;

use Code16\Sharp\Auth\SharpAuthorizationManager;
use Code16\Sharp\Exceptions\SharpInvalidConfigException;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Inertia\Inertia;

class EntityListController extends SharpProtectedController
{
    public function __construct(
        private SharpAuthorizationManager $sharpAuthorizationManager,
        private SharpEntityManager $entityManager,
    ) {
        parent::__construct();
    }

    public function show(string $entityKey)
    {
        sharp_check_ability('entity', $entityKey);

        $list = $this->entityManager->entityFor($entityKey)->getListOrFail();
        $list->buildListConfig();
        $list->initQueryParams();

        $data = [
            'entityKey' => $entityKey,
            'containers' => $list->fields(),
            'layout' => $list->listLayout(),
            'data' => $list->data(),
            'fields' => $list->listMetaFields(),
            'config' => $list->listConfig(
                $this->entityManager->entityFor($entityKey)->hasShow(),
            ),
        ];

        $data['authorizations'] = $this->getAuthorizationsForEntityList(
            $entityKey, 
            $data['data']['list']['items'], 
            $data['config'],
        );
        
        $data['forms'] = $this->getMultiformDataForEntityList(
            $entityKey, 
            $data['data']['list']['items'], 
            $data['config'],
        );
        
        $data['notifications'] = $this->getSharpNotifications();
        
        // TODO handle breadcrumb

        return Inertia::render('List', $data);
    }

    private function getAuthorizationsForEntityList(string $entityKey, array $listItems, array $listConfig): array
    {
        $authorizations = [
            'view' => [],
            'update' => [],
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
            });

        return $authorizations;
    }

    private function getMultiformDataForEntityList(string $entityKey, array $listItems, array $listConfig): array
    {
        if ($listConfig['multiformAttribute'] === null) {
            return [];
        }

        if (! $forms = $this->entityManager->entityFor($entityKey)->getMultiforms()) {
            throw new SharpInvalidConfigException(
                'The list for the entity '.$entityKey.' defines a multiform attribute ['
                .$listConfig['multiformAttribute']
                .' but the entity is not configured as multiform.'
            );
        }

        return collect($forms)
            ->map(function ($value, $key) use ($listConfig, $listItems) {
                $instanceIds = collect($listItems)
                    ->where($listConfig['multiformAttribute'], $key)
                    ->pluck($listConfig['instanceIdAttribute']);

                return [
                    'key' => $key,
                    'label' => is_array($value) && sizeof($value) > 1 ? $value[1] : $key,
                    'instances' => $instanceIds,
                ];
            })
            ->keyBy('key')
            ->all();
    }

    private function getSharpNotifications(): array
    {
        if ($notifications = session('sharp_notifications')) {
            session()->forget('sharp_notifications');

            return array_values($notifications);
        }
        
        return [];
    }
}
