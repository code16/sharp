<?php

namespace Code16\Sharp\Utils\Entities;

use Code16\Sharp\Exceptions\SharpInvalidEntityKeyException;

class SharpEntityManager
{
    public function entityFor(string $entityKey): SharpEntity|SharpDashboardEntity
    {
        $entityKey = sharp_normalize_entity_key($entityKey)[0];

        if (!$entity = config("sharp.entities.$entityKey")) {
            if (!$entity = config("sharp.dashboards.$entityKey")) {
                throw new SharpInvalidEntityKeyException("The entity [{$entityKey}] was not found.");
            }
        }

        if (is_string($entity)) {
            // New Sharp 7 format: SharpEntity
            if (!app()->bound($entity)) {
                app()->singleton($entity, function () use ($entity, $entityKey) {
                    return (new $entity())->setEntityKey($entityKey);
                });
            }

            return app($entity);
        }

        // Old array config format is used
        if (isset($entity['view'])) {
            // This is a Dashboard
            return new class($entity) extends SharpDashboardEntity {
                public function __construct(private array $entity)
                {
                    $this->view = $this->entity['view'];
                    $this->policy = $this->entity['policy'] ?? null;
                }
            };
        }

        return new class($entity, $entityKey) extends SharpEntity {
            public function __construct(private array $entity, string $entityKey)
            {
                $this->entityKey = $entityKey;
                $this->label = $this->entity['label'] ?? 'Entity';
                $this->isSingle = $this->entity['single'] ?? false;
                $this->list = $this->entity['list'] ?? null;
                $this->show = $this->entity['show'] ?? null;
                $this->form = $this->entity['form'] ?? null;
                $this->policy = $this->entity['policy'] ?? null;
                $this->prohibitedActions = collect($this->entity['authorizations'] ?? [])
                    ->filter(function ($value) {
                        return $value === false;
                    })
                    ->keys()
                    ->toArray();
            }

            public function getMultiforms(): array
            {
                return collect($this->entity['forms'] ?? [])
                    ->mapWithKeys(function ($values, $key) {
                        return [$key => [$values['form'], $values['label']]];
                    })
                    ->toArray();
            }
        };
    }
}
