<?php

namespace Code16\Sharp\Utils\Entities;

use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Exceptions\SharpInvalidEntityKeyException;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Show\SharpShow;

abstract class SharpEntity extends BaseSharpEntity
{
    protected bool $isSingle = false;
    protected ?string $list = null;
    protected ?string $form = null;
    protected ?string $show = null;
    protected array $prohibitedActions = [];

    final public function getListOrFail(): SharpEntityList
    {
        if (! $list = $this->getList()) {
            throw new SharpInvalidEntityKeyException(
                sprintf('The list for the entity [%s] was not found.', get_class($this))
            );
        }

        return $list instanceof SharpEntityList ? $list : app($list);
    }

    final public function getShowOrFail(): SharpShow
    {
        if (! $show = $this->getShow()) {
            throw new SharpInvalidEntityKeyException(
                sprintf('The show for the entity [%s] was not found.', get_class($this))
            );
        }

        return instanciate($show);
    }

    final public function hasShow(): bool
    {
        return $this->getShow() !== null;
    }

    final public function hasForm(): bool
    {
        return $this->getForm() !== null;
    }

    final public function getFormOrFail(?string $multiformKey = null): SharpForm
    {
        if ($multiformKey) {
            if (count($this->getMultiforms())) {
                if (! $form = ($this->getMultiforms()[$multiformKey][0] ?? null)) {
                    throw new SharpInvalidEntityKeyException(
                        sprintf('The subform for the entity [%s:%s] was not found.', get_class($this), $multiformKey)
                    );
                }

                return instanciate($form);
            }
        }

        if (! $form = $this->getForm()) {
            throw new SharpInvalidEntityKeyException(
                sprintf('The form for the entity [%s] was not found.', get_class($this))
            );
        }

        return instanciate($form);
    }

    final public function getLabelOrFail(?string $multiformKey = null): string
    {
        $label = $multiformKey
            ? $this->getMultiforms()[$multiformKey][1] ?? null
            : $this->getLabel();

        if ($label === null) {
            throw new SharpInvalidEntityKeyException(
                sprintf('The label of the subform for the entity [%s:%s] was not found.', get_class($this), $multiformKey)
            );
        }

        return $label;
    }

    final public function isActionProhibited(string $action): bool
    {
        return in_array($action, $this->prohibitedActions);
    }

    final public function isSingle(): bool
    {
        return $this->isSingle;
    }

    protected function getLabel(): string
    {
        return $this->label;
    }

    protected function getList(): ?SharpEntityList
    {
        if ($this->isSingle) {
            throw new SharpInvalidEntityKeyException(
                sprintf('The entity [%s] is single, and does not have a list.', get_class($this))
            );
        }

        return $this->list ? app($this->list) : null;
    }

    protected function getShow(): ?SharpShow
    {
        return $this->show ? app($this->show) : null;
    }

    protected function getForm(): ?SharpForm
    {
        return $this->form ? app($this->form) : null;
    }

    /**
     * @deprecated
     * @see SharpEntityList::configureEntityMap()
     */
    public function getMultiforms(): array
    {
        return [];
    }
}
