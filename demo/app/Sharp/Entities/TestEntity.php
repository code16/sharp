<?php

namespace App\Sharp\Entities;

use Code16\Sharp\Utils\Entities\SharpEntity;

class TestEntity extends SharpEntity
{
    protected bool $isSingle = true;
    protected ?string $show = \App\Sharp\TestForm\TestShow::class;
    protected ?string $form = \App\Sharp\TestForm\TestForm::class;
    protected ?string $policy = \App\Sharp\TestForm\TestPolicy::class;
    protected string $label = 'Test';
}
