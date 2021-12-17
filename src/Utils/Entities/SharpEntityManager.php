<?php

namespace Code16\Sharp\Utils\Entities;

use Code16\Sharp\Exceptions\SharpInvalidEntityKeyException;
use Code16\Sharp\Show\SharpShow;
use Code16\Sharp\Show\SharpSingleShow;

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
            }

            protected function getShow(): string|SharpShow|SharpSingleShow|null
            {
                return $this->entity["show"] ?? null;
            }
        };
    }
}