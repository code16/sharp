<?php

namespace Code16\Sharp\Tests\Fixtures\Entities;

use Code16\Sharp\Tests\Fixtures\Sharp\PersonSingleForm;

class SinglePersonEntity extends PersonEntity
{
    public static string $entityKey = 'single-person';
    protected bool $isSingle = true;
    protected ?string $form = PersonSingleForm::class;
}
