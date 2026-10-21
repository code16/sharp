<?php

namespace App\Sharp\Entities;

use App\Sharp\TestModels\TestModelFormReadonly;

class TestModelReadonlyEntity extends TestModelEntity
{
    public static string $entityKey = 'test-models-readonly';
    protected string $label = 'Test model (all fields read-only)';
    protected ?string $form = TestModelFormReadonly::class;
}
