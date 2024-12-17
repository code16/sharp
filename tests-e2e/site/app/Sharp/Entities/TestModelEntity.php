<?php

namespace App\Sharp\Entities;

use App\Sharp\TestModels\TestModelForm;
use App\Sharp\TestModels\TestModelList;
use App\Sharp\TestModels\TestModelShow;
use Code16\Sharp\Utils\Entities\SharpEntity;

class TestModelEntity extends SharpEntity
{
    protected string $label = 'Test model';
    protected ?string $list = TestModelList::class;
    protected ?string $show = TestModelShow::class;
    protected ?string $form = TestModelForm::class;
    protected array $prohibitedActions = [];
}
