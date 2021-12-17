<?php

namespace App\Sharp\Entities;

use Code16\Sharp\Utils\Entities\SharpDefaultEntity;

class TestEntity extends SharpDefaultEntity
{
    protected bool $isSingle = true;
    
    public function getShow(): ?string
    {
        return \App\Sharp\TestForm\TestShow::class;
    }

    public function getForm(): ?string
    {
        return \App\Sharp\TestForm\TestForm::class;
    }

    public function getLabel(): ?string
    {
        return "Test";
    }
}