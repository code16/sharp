<?php

namespace App\Sharp\Entities;

use App\Sharp\TestModels\TestModelForm;
use App\Sharp\TestModels\TestModelFormReadonly;
use App\Sharp\TestModels\TestModelFormRequired;
use App\Sharp\TestModels\TestModelFormTabs;
use App\Sharp\TestModels\TestModelList;
use App\Sharp\TestModels\TestModelShow;
use Code16\Sharp\Utils\Entities\SharpEntity;

class TestModelEntity extends SharpEntity
{
    protected string $label = 'Test model';
    public string $entityKey = 'test-models';
    protected ?string $list = TestModelList::class;
    protected ?string $show = TestModelShow::class;
    protected ?string $form = TestModelForm::class;
    protected array $prohibitedActions = [];

    public function getMultiforms(): array
    {
        return [
            'required' => [TestModelFormRequired::class, 'Test model (all fields required)'],
            'readonly' => [TestModelFormReadonly::class, 'Test model (all fields read-only)'],
            'tabs' => [TestModelFormTabs::class, 'Test model (with tabs)'],
        ];
    }
}
