<?php

namespace Code16\Sharp\Tests\Fixtures\Entities;

class SinglePersonEntity extends PErsonEntity
{
    protected bool $isSingle = true;
    protected ?string $form = PersonSingleForm::class;
}
