<?php

namespace Code16\Sharp\Tests\Fixtures\Entities;

use Closure;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Show\SharpShow;
use Code16\Sharp\Utils\Entities\SharpEntity;

class PersonEntity extends SharpEntity
{
    public ?string $validatorForTest = null;
    public array $multiformValidatorsForTest = [];
    public array $multiformForTest = [];
    protected string $entityKey = 'person';
    protected string $label = 'person';
    protected ?string $list = PersonList::class;
    protected ?string $form = PersonForm::class;
    protected ?SharpForm $fakeForm = null;
    protected ?string $show = PersonShow::class;
    protected ?SharpShow $fakeShow = null;

    public function setList(string $list): self
    {
        $this->list = $list;

        return $this;
    }

    public function setShow(?SharpShow $show): self
    {
        $this->fakeShow = $show;

        return $this;
    }

    public function setForm(?SharpForm $form): self
    {
        $this->fakeForm = $form;

        return $this;
    }

    public function getForm(): SharpForm
    {
        return $this->fakeForm ?? parent::getForm();
    }

    protected function getShow(): SharpShow
    {
        return $this->fakeShow ?? parent::getShow();
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
        $this->multiformForTest = $multiform;

        return $this;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getMultiforms(): array
    {
        return $this->multiformForTest;
    }

    public function setPolicy(string $policy): self
    {
        $this->policy = $policy;

        return $this;
    }

    public function setProhibitedActions(array $prohibitedActions): self
    {
        $this->prohibitedActions = $prohibitedActions;

        return $this;
    }
}
