<?php

namespace App\Sharp\Entities;

use App\Sharp\TestModels\TestModelFormRequired;

class TestModelRequiredEntity extends TestModelEntity
{
    public static string $entityKey = 'test-models-required';
    protected string $label = 'Test model (all fields required)';
    protected ?string $form = TestModelFormRequired::class;
}
