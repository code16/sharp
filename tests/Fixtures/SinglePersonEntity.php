<?php

namespace Code16\Sharp\Tests\Fixtures;

class SinglePersonEntity extends PersonEntity
{
    protected ?string $show = PersonSharpSingleShow::class;
}