<?php

namespace Code16\Sharp\Tests\Fixtures\Entities;

use Code16\Sharp\Auth\SharpEntityPolicy;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Show\SharpShow;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonForm;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonList;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonShow;
use Code16\Sharp\Utils\Entities\SharpEntity;

class PersonEntity extends SharpEntity
{
    public static string $entityKey = 'person';
    public ?string $validatorForTest = null;
    public array $multiformValidatorsForTest = [];
    public ?array $fakeMultiforms = null;
    protected string $label = 'person';
    protected ?string $list = PersonList::class;
    protected ?SharpEntityList $fakeList;
    protected ?string $form = PersonForm::class;
    protected ?SharpForm $fakeForm;
    protected ?string $show = PersonShow::class;
    protected ?SharpShow $fakeShow;
    protected ?SharpEntityPolicy $fakePolicy = null;

    public function setList(?SharpEntityList $list): self
    {
        $this->fakeList = $list;

        return $this;
    }

    public function setShow(?SharpShow $show): self
    {
        $this->fakeShow = $show;
        if ($show === null) {
            // Enforce show to be null
            $this->show = null;
        }

        return $this;
    }

    public function setForm(?SharpForm $form): self
    {
        $this->fakeForm = $form;
        if ($form === null) {
            // Enforce show to be null
            $this->form = null;
        }

        return $this;
    }

    public function getForm(): ?SharpForm
    {
        return $this->fakeForm ?? parent::getForm();
    }

    protected function getShow(): ?SharpShow
    {
        return $this->fakeShow ?? parent::getShow();
    }

    protected function getList(): ?SharpEntityList
    {
        return $this->fakeList ?? parent::getList();
    }

    public function setValidator(string $validatorClass, ?string $subentity = null): self
    {
        if (! $subentity) {
            $this->validatorForTest = $validatorClass;
        } else {
            $this->multiformValidatorsForTest[$subentity] = $validatorClass;
        }

        return $this;
    }

    public function setMultiforms(array $multiform): self
    {
        $this->fakeMultiforms = $multiform;

        return $this;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getMultiforms(): array
    {
        return $this->fakeMultiforms ?? parent::getMultiforms();
    }

    public function setPolicy(SharpEntityPolicy $policy): self
    {
        $this->fakePolicy = $policy;

        return $this;
    }

    protected function getPolicy(): ?SharpEntityPolicy
    {
        return $this->fakePolicy ?? parent::getPolicy();
    }

    public function setProhibitedActions(array $prohibitedActions): self
    {
        $this->prohibitedActions = $prohibitedActions;

        return $this;
    }
}
