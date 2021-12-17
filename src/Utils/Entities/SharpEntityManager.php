<?php

namespace Code16\Sharp\Utils\Entities;

use Code16\Sharp\Exceptions\SharpInvalidEntityKeyException;

class SharpEntityManager
{
    public function entityFor(string $entityKey): SharpEntity
    {
        if(!$entity = config("sharp.entities.$entityKey")) {
            throw new SharpInvalidEntityKeyException("The entity [{$entityKey}] was not found.");
        }
        
        if(is_string($entity)) {
            return new $entity($entityKey);
        }
        
        // Old array config format is used
        return new class($entity, $entityKey) extends SharpEntity {
            private array $entity;
            public function __construct(array $entity, string $entityKey)
            {
                parent::__construct($entityKey);
                $this->entity = $entity;
                $this->label = $this->entity["label"];
                $this->isSingle = $this->entity["single"] ?? false;
                $this->list = $this->entity["list"] ?? null;
                $this->show = $this->entity["show"] ?? null;
                $this->form = $this->entity["form"] ?? null;
            }

            public function getMultiforms(): array
            {
                return collect($this->entity["forms"] ?? [])
                    ->mapWithKeys(function($values, $key) {
                        return [$key => [$values["form"], $values["label"]]];
                    })
                    ->toArray();
                }
        };
    }
}