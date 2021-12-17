<?php

namespace Code16\Sharp\Utils\Entities;

use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Exceptions\SharpInvalidEntityKeyException;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Show\SharpShow;
use Illuminate\Foundation\Http\FormRequest;

abstract class SharpEntity
{
    public function __construct(protected string $entityKey)
    {
    }

    public function getShowOrFail(): SharpShow
    {
        if(!$show = $this->getShow()) {
            throw new SharpInvalidEntityKeyException("The show for the entity [{$this->entityKey}] was not found.");
        }
        
        return app($show);
    }

    abstract protected function getShow(): ?string;

    public final function getFormOrFail(): SharpForm
    {
        if(!$form = $this->getForm()) {
            throw new SharpInvalidEntityKeyException("The form for the entity [{$this->entityKey}] was not found.");
        }

        return app($form);
    }

    abstract protected function getForm(): ?string;
    
    // authorizations

    public final function getListOrFail(): SharpEntityList
    {
        if(!$list = $this->getList()) {
            throw new SharpInvalidEntityKeyException("The list for the entity [{$this->entityKey}] was not found.");
        }

        return app($list);
    }

    abstract protected function getList(): ?string;

    public final function getFormValidatorOrFail(): FormRequest
    {
        if(!$validator = $this->getFormValidator()) {
            throw new SharpInvalidEntityKeyException("The form validator for the entity [{$this->entityKey}] was not found.");
        }

        return is_string($validator) ? app($validator) : $validator;
    }

    abstract protected function getFormValidator(): ?string;

    abstract protected function getPolicy(): ?string;

    abstract protected function getLabel(): ?string;

    abstract protected function getGlobalAuthorizationForEntity(): bool;
    abstract protected function getGlobalAuthorizationForCreate(): bool;
    abstract protected function getGlobalAuthorizationForDelete(): bool;
    abstract protected function getGlobalAuthorizationForUpdate(): bool;
    abstract protected function getGlobalAuthorizationForView(): bool;
}