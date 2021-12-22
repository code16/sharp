<?php

namespace Code16\Sharp\Utils\Entities;

use Code16\Sharp\Auth\SharpEntityPolicy;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Exceptions\SharpInvalidEntityKeyException;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Show\SharpShow;

abstract class SharpEntity
{
    protected string $entityKey = "entity";
    protected bool $isSingle = false;
    protected string $label = "entity";
    protected ?string $list = null;
    protected ?string $form = null;
    protected ?string $show = null;
    protected ?string $policy = null;

    public function setEntityKey(string $entityKey): self
    {
        $this->entityKey = $entityKey;
        return $this;
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
        throw_if(
            !$this->hasShow(), 
            new SharpInvalidEntityKeyException("The show for the entity [{$this->entityKey}] was not found.")
        );
        return app($this->getShow());
    }

    public function hasShow(): bool
    {
        return $this->getShow() !== null;
    }

    public final function getFormOrFail(?string $subEntity = null): SharpForm
    {
        if($subEntity) {
            if(!$form = ($this->getMultiforms()[$subEntity][0] ?? null)) {
                throw new SharpInvalidEntityKeyException("The subform for the entity [{$this->entityKey}:{$subEntity}] was not found.");
            }
        } elseif(!$form = $this->getForm()) {
            throw new SharpInvalidEntityKeyException("The form for the entity [{$this->entityKey}] was not found.");
        }
        return app($form);
    }

    public final function getPolicyOrDefault(): SharpEntityPolicy
    {
        if(!$policy = $this->getPolicy()) {
            return new SharpEntityPolicy();
        }
        return app($policy);
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

    protected function getPolicy(): ?string
    {
        return $this->policy;
    }

    public function getMultiforms(): array
    {
        return [];
    }
}