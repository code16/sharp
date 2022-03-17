<?php

namespace Code16\Sharp\Tests\Fixtures;

class SinglePersonEntity extends PersonEntity
{
    protected bool $isSingle = true;
    protected ?string $show = PersonSharpSingleShow::class;
    protected ?string $form = PersonSharpSingleForm::class;
}
