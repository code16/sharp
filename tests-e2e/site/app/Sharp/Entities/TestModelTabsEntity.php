<?php

namespace App\Sharp\Entities;

use App\Sharp\TestModels\TestModelFormTabs;

class TestModelTabsEntity extends TestModelEntity
{
    public static string $entityKey = 'test-models-tabs';
    protected string $label = 'Test model (with tabs)';
    protected ?string $form = TestModelFormTabs::class;
}
