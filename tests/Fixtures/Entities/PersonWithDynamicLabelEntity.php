<?php

namespace Code16\Sharp\Tests\Fixtures\Entities;

class PersonWithDynamicLabelEntity extends PersonEntity
{
    protected function getLabel(): string
    {
        return now()->format('Ymd');
    }
}
