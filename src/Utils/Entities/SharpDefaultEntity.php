<?php

namespace Code16\Sharp\Utils\Entities;

use Code16\Sharp\Exceptions\SharpInvalidEntityKeyException;

class SharpDefaultEntity extends SharpEntity
{
    protected bool $isSingle = false;

    protected function getList(): ?string
    {
        return $this->isSingle 
            ? throw new SharpInvalidEntityKeyException("The entity [{$this->entityKey}] is single, and does not have a list.")
            : null;
    }

    protected function getShow(): ?string
    {
        return null;
    }

    protected function getForm(): ?string
    {
        return null;
    }

    protected function getFormValidator(): ?string
    {
        return null;
    }

    protected function getPolicy(): ?string
    {
        return null;
    }

    protected function getLabel(): ?string
    {
        return null;
    }

    protected function getGlobalAuthorizationForEntity(): bool
    {
        return true;
    }

    protected function getGlobalAuthorizationForCreate(): bool
    {
        return true;
    }

    protected function getGlobalAuthorizationForDelete(): bool
    {
        return true;
    }

    protected function getGlobalAuthorizationForUpdate(): bool
    {
        return true;
    }

    protected function getGlobalAuthorizationForView(): bool
    {
        return true;
    }
}