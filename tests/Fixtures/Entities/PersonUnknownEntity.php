<?php

namespace Code16\Sharp\Tests\Fixtures\Entities;

class PersonUnknownEntity extends PersonEntity
{
    public static string $entityKey = 'person-unknown';
    protected string $label = 'Unknown';
    protected ?string $form = null;
    protected ?string $show = null;
}
