<?php

namespace Code16\Sharp\Utils\Entities;

use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Exceptions\SharpInvalidEntityKeyException;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Show\SharpShow;
use Illuminate\Foundation\Http\FormRequest;

abstract class SharpEntity
{
    protected string $entityKey;
    protected bool $isSingle = false;
    protected string $label = "entity";
    protected ?string $list = null;
    protected ?string $form = null;
    protected ?string $show = null;
    
    public function __construct(string $entityKey)
    {
        $this->entityKey = $entityKey;
    }

    public final function getListOrFail(): SharpEntityList
    {
        if(!$list = $this->getList()) {
            throw new SharpInvalidEntityKeyException("The list for the entity [{$this->entityKey}] was not found.");
        }

        return app($list);
    }

    public function getShowOrFail(): SharpShow
    {
        if(!$show = $this->getShow()) {
            throw new SharpInvalidEntityKeyException("The show for the entity [{$this->entityKey}] was not found.");
        }
        
        return app($show);
    }

    public final function getFormOrFail(): SharpForm
    {
        if(!$form = $this->getForm()) {
            throw new SharpInvalidEntityKeyException("The form for the entity [{$this->entityKey}] was not found.");
        }

        return app($form);
    }

    public final function getFormValidatorOrFail(): FormRequest
    {
        if(!$validator = $this->getFormValidator()) {
            throw new SharpInvalidEntityKeyException("The form validator for the entity [{$this->entityKey}] was not found.");
        }

        return is_string($validator) ? app($validator) : $validator;
    }

    protected function getList(): ?string
    {
        return $this->isSingle
            ? throw new SharpInvalidEntityKeyException("The entity [{$this->entityKey}] is single, and does not have a list.")
            : $this->list;
    }

    protected function getShow(): ?string
    {
        return $this->show;
    }

    protected function getForm(): ?string
    {
        return $this->form;
    }

    // authorizations

    public function getMultiforms(): array
    {
        return [];
    }

//    abstract protected function getFormValidator(): ?string;
//
//    abstract protected function getPolicy(): ?string;
//
//    abstract protected function getLabel(): ?string;
//
//    abstract protected function getGlobalAuthorizationForEntity(): bool;
//    abstract protected function getGlobalAuthorizationForCreate(): bool;
//    abstract protected function getGlobalAuthorizationForDelete(): bool;
//    abstract protected function getGlobalAuthorizationForUpdate(): bool;
//    abstract protected function getGlobalAuthorizationForView(): bool;
}