<?php

namespace Code16\Sharp\Tests\Fixtures;

use Code16\Sharp\Utils\Entities\SharpEntity;

class PersonEntity extends SharpEntity
{
    public ?string $validatorForTest = null;
    public array $multiformValidatorsForTest = [];
    public array $multiformForTest = [];
    protected string $entityKey = 'person';
    protected string $label = 'person';
    protected ?string $list = PersonSharpEntityList::class;
    protected ?string $form = PersonSharpForm::class;
    protected ?string $show = PersonSharpShow::class;

    public function setList(string $list): self
    {
        $this->list = $list;

        return $this;
    }

    public function setShow(?string $show): self
    {
        $this->show = $show;

        return $this;
    }

    public function setForm(?string $form): self
    {
        $this->form = $form;

        return $this;
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
